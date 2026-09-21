<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_by_customer_and_shares_the_filter_for_the_banner(): void
    {
        $ada = Customer::factory()->create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        $adasOrder = Order::factory()->create(['customer_id' => $ada->id]);
        Order::factory()->create(['customer_id' => Customer::factory()->create()->id]);

        $this->get(route('orders.index', ['customer_id' => $ada->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('customerFilter.id', $ada->id)
                ->where('customerFilter.name', 'Ada Lovelace')
                ->has('orders.data', 1)
                ->where('orders.data.0.id', $adasOrder->id));
    }

    public function test_an_unknown_customer_id_is_ignored_rather_than_erroring(): void
    {
        Order::factory()->count(2)->create();

        $this->get(route('orders.index', ['customer_id' => 999999]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('customerFilter', null)
                ->has('orders.data', 2));
    }

    public function test_it_filters_by_a_known_status(): void
    {
        $completed = Order::factory()->create(['status' => Order::STATUS_COMPLETED]);
        Order::factory()->create(['status' => Order::STATUS_CANCELLED]);

        $this->get(route('orders.index', ['status' => Order::STATUS_COMPLETED]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('status', Order::STATUS_COMPLETED)
                ->has('orders.data', 1)
                ->where('orders.data.0.id', $completed->id));
    }

    public function test_it_exposes_the_selectable_statuses(): void
    {
        $this->get(route('orders.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('statuses', Order::statuses()));
    }

    public function test_it_ignores_an_unknown_status(): void
    {
        Order::factory()->count(2)->create(['status' => Order::STATUS_COMPLETED]);

        $this->get(route('orders.index', ['status' => 'bogus']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('status', null)
                ->has('orders.data', 2));
    }
}
