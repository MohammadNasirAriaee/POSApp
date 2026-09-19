<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelPendingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_a_pending_order_does_not_invent_stock(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);

        // A pending order never decremented stock, so cancelling it must not
        // add any back.
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);
        $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 4,
            'subtotal' => 4 * (float) $product->price,
        ]);

        $this->delete(route('orders.cancel', $order));

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock_quantity);
    }
}
