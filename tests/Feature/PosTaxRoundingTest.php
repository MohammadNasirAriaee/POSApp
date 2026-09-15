<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTaxRoundingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_stored_total_is_the_sum_of_its_parts(): void
    {
        // 3 x 3.33 = 9.99 -> tax 0.999 which must round, not truncate.
        $product = Product::factory()->create([
            'price' => 3.33,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 3]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ]);

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame('9.99', $order->subtotal);
        $this->assertSame('1.00', $order->tax);
        $this->assertSame('0.00', $order->discount);
        $this->assertSame(
            round((float) $order->subtotal + (float) $order->tax - (float) $order->discount, 2),
            round((float) $order->total, 2),
            'Stored total must equal subtotal + tax - discount.'
        );
    }

    public function test_a_discount_never_pushes_the_total_below_zero(): void
    {
        $product = Product::factory()->create([
            'price' => 5.00,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 1]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 9999,
        ]);

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame('0.00', $order->total);
        $this->assertSame('5.50', $order->discount);
    }
}
