<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderReceiptTest extends TestCase
{
    use RefreshDatabase;

    private function checkout(Product $product, array $overrides = [])
    {
        return $this->post(route('pos.checkout'), array_merge([
            'cart' => [['id' => $product->id, 'quantity' => 1]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ], $overrides));
    }

    public function test_the_receipt_reports_the_tendered_amount_and_change(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        // total is 11.00; cashier takes a 20.
        $this->checkout($product, ['tendered' => 20]);

        $order = Order::latest('id')->firstOrFail();

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('order.tendered', '20.00')
                ->where('order.change', '9.00'));
    }

    public function test_change_is_zero_when_no_tender_was_recorded(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->checkout($product);

        $order = Order::latest('id')->firstOrFail();

        $this->assertNull($order->tendered);
        $this->assertSame('0.00', $order->change);
    }

    public function test_a_tender_below_the_total_is_rejected(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->checkout($product, ['tendered' => 1])->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock_quantity);
    }

    public function test_an_exact_tender_is_accepted(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->checkout($product, ['tendered' => 11.00]);

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('0.00', $order->change);
    }

    public function test_the_receipt_does_not_eager_load_the_live_product(): void
    {
        // Items snapshot name/price at sale time precisely so a receipt never
        // has to reach for the live product - loading it would be wasted work.
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->checkout($product, ['tendered' => 11]);
        $order = Order::latest('id')->firstOrFail();

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->missing('order.items.0.product'));
    }
}
