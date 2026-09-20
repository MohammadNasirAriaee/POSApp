<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_show_route_redirects_to_edit_instead_of_crashing(): void
    {
        // Route::resource registers customers.show (GET /customers/{customer})
        // whether or not there is a dedicated detail page; without a show()
        // method visiting that URL is a fatal error, not a 404.
        $customer = Customer::factory()->create();

        $this->get(route('customers.show', $customer))
            ->assertRedirect(route('customers.edit', $customer));
    }

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

    public function test_emails_that_differ_only_by_case_are_rejected_as_duplicates(): void
    {
        Customer::factory()->create(['email' => 'ada@example.com']);

        $this->post(route('customers.store'), [
            'first_name' => 'Someone',
            'last_name' => 'Else',
            'email' => 'ADA@EXAMPLE.COM',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Customer::count());
    }

    public function test_a_new_customer_email_is_stored_lowercased(): void
    {
        $this->post(route('customers.store'), [
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'Grace.Hopper@Example.COM',
        ]);

        $this->assertDatabaseHas('customers', ['email' => 'grace.hopper@example.com']);
    }

    public function test_a_blank_email_is_still_accepted_since_it_is_optional(): void
    {
        $this->post(route('customers.store'), [
            'first_name' => 'Walk',
            'last_name' => 'In',
            'email' => '',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', ['first_name' => 'Walk', 'email' => null]);
    }
}
