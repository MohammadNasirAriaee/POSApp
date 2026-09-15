<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_across_name_email_and_phone(): void
    {
        $byLastName = Customer::factory()->create(['first_name' => 'Ada', 'last_name' => 'Zephyr']);
        $byEmail = Customer::factory()->create(['first_name' => 'Bob', 'email' => 'zephyr@example.com']);
        $byPhone = Customer::factory()->create(['first_name' => 'Cid', 'phone' => '0790909090']);
        Customer::factory()->create(['first_name' => 'Dee', 'last_name' => 'Nomatch']);

        $this->get(route('customers.index', ['search' => 'Zephyr']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('customers.data', 2));

        $this->get(route('customers.index', ['search' => '0790909090']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $byPhone->id));

        $this->assertNotNull($byLastName->id);
        $this->assertNotNull($byEmail->id);
    }

    public function test_it_returns_all_customers_without_a_search_term(): void
    {
        Customer::factory()->count(4)->create();

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('customers.data', 4));
    }
}
