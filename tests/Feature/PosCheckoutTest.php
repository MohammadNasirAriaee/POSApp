<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $cart, array $overrides = []): array
    {
        return array_merge([
            'cart' => $cart,
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ], $overrides);
    }

    public function test_it_records_the_sale_and_decrements_stock(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), $this->payload([
            ['id' => $product->id, 'quantity' => 2],
        ]));

        $order = Order::latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('20.00', $order->subtotal);
        $this->assertSame('2.00', $order->tax);
        $this->assertSame('22.00', $order->total);
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_it_refuses_to_sell_an_inactive_product(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'status' => Product::STATUS_DRAFT,
        ]);

        $this->post(route('pos.checkout'), $this->payload([
            ['id' => $product->id, 'quantity' => 1],
        ]))->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock_quantity);
    }

    public function test_it_refuses_to_oversell_and_leaves_stock_untouched(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), $this->payload([
            ['id' => $product->id, 'quantity' => 4],
        ]))->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }

    public function test_it_prices_from_the_database_not_the_cart_payload(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), $this->payload([
            ['id' => $product->id, 'quantity' => 1, 'price' => 0.01],
        ]));

        $this->assertSame('10.00', Order::latest('id')->first()->subtotal);
    }
}
