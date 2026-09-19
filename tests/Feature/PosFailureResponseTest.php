<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosFailureResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_failed_checkout_redirects_with_a_flash_error_not_a_validation_error(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $response = $this->from(route('pos.index'))->post(route('pos.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 5]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ]);

        // A 302 with a flash error is what the client sees as a *successful*
        // Inertia visit, so the page must not treat it as a completed sale.
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('error');
    }
}
