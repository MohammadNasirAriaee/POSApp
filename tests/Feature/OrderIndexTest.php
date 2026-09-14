<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

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
