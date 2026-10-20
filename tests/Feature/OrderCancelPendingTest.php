<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers cancelling a pending (not yet completed) order.
 */
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
        OrderItem::factory()->forProduct($product, 4)->create(['order_id' => $order->id]);

        $response = $this->delete(route('orders.cancel', $order));

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock_quantity);

        // Nothing was put back into stock, so the flash message must not
        // claim that it was.
        $response->assertSessionHas('success', 'Order cancelled successfully.');
    }

    public function test_cancelling_with_no_referer_falls_back_to_the_orders_list(): void
    {
        // back() has nothing to fall back to without a Referer header (a
        // direct request, not a click from within the app); it must land on
        // the orders list rather than the bare app root.
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $this->delete(route('orders.cancel', $order))
            ->assertRedirect(route('orders.index'));
    }
}
