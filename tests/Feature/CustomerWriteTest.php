<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_customer(): void
    {
        $this->post(route('customers.store'), [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'phone' => '0790000001',
            'address' => '1 High Street',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['email' => 'ada@example.com']);
    }

    public function test_it_updates_a_customer_without_tripping_its_own_unique_email(): void
    {
        $customer = Customer::factory()->create(['email' => 'keep@example.com']);

        $this->put(route('customers.update', $customer), [
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'keep@example.com',
            'phone' => $customer->phone,
            'address' => null,
        ])->assertRedirect(route('customers.index'));

        $this->assertSame('Grace', $customer->fresh()->first_name);
    }

    public function test_it_rejects_an_email_already_used_by_someone_else(): void
    {
        Customer::factory()->create(['email' => 'taken@example.com']);
        $customer = Customer::factory()->create();

        $this->put(route('customers.update', $customer), [
            'first_name' => 'X',
            'email' => 'taken@example.com',
        ])->assertSessionHasErrors('email');
    }
}
