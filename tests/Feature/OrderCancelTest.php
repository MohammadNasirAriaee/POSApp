<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_an_order_returns_its_items_to_stock(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
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
        $this->assertSame(7, $product->fresh()->stock_quantity);

        $this->delete(route('orders.cancel', $order))
            ->assertSessionHas('success', 'Order cancelled and stock returned successfully.');

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_cancelling_twice_does_not_return_stock_again(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->post(route('pos.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 2]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ]);

        $order = Order::latest('id')->firstOrFail();

        $this->delete(route('orders.cancel', $order));
        $this->delete(route('orders.cancel', $order))->assertSessionHas('error');

        $this->assertSame(10, $product->fresh()->stock_quantity);
    }
}
