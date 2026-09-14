<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sums_only_todays_completed_orders(): void
    {
        Order::factory()->create(['total' => 100, 'status' => Order::STATUS_COMPLETED]);
        Order::factory()->create(['total' => 50, 'status' => Order::STATUS_COMPLETED]);
        Order::factory()->create(['total' => 999, 'status' => Order::STATUS_CANCELLED]);
        Order::factory()->create(['total' => 777, 'status' => Order::STATUS_COMPLETED])
            ->forceFill(['created_at' => now()->subDays(2)])->save();

        Customer::factory()->count(3)->create();
        Product::factory()->create(['stock_quantity' => 1, 'status' => Product::STATUS_ACTIVE]);
        Product::factory()->create(['stock_quantity' => 99, 'status' => Product::STATUS_ACTIVE]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('stats.today_sales', 150)
                ->where('stats.today_orders', 2)
                ->where('stats.total_products', 2)
                ->where('stats.total_customers', 3)
                ->where('stats.low_stock_products', 1));
    }

    public function test_it_reports_zero_sales_on_an_empty_day(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('stats.today_sales', 0)
                ->where('stats.today_orders', 0));
    }
}
