<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Database\Seeders\OrderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_spreads_seeded_orders_across_past_dates(): void
    {
        Product::factory()->count(5)->create(['status' => Product::STATUS_ACTIVE]);

        $this->seed(OrderSeeder::class);

        $orders = Order::all();
        $this->assertCount(15, $orders);

        // With dates spread over 30 days, they cannot all land on today.
        $distinctDays = $orders->pluck('created_at')->map->toDateString()->unique();
        $this->assertGreaterThan(1, $distinctDays->count(), 'Seeded orders all share one date.');

        // Items inherit their order's date rather than "now".
        $order = $orders->firstWhere(fn ($o) => ! $o->created_at->isToday());
        $this->assertNotNull($order, 'Expected at least one order dated before today.');
        $this->assertSame(
            $order->created_at->toDateString(),
            $order->items()->first()->created_at->toDateString()
        );
    }

    public function test_it_seeds_without_customers_or_employees(): void
    {
        Product::factory()->count(3)->create(['status' => Product::STATUS_ACTIVE]);

        $this->seed(OrderSeeder::class);

        $this->assertSame(15, Order::count());
    }

    public function test_seeded_payment_methods_pass_checkout_validation(): void
    {
        Product::factory()->count(3)->create(['status' => Product::STATUS_ACTIVE]);

        $this->seed(OrderSeeder::class);

        $allowed = ['cash', 'card', 'bank_transfer'];
        foreach (Order::pluck('payment_method') as $method) {
            $this->assertContains($method, $allowed);
        }
    }
}
