<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_receipt_receives_the_configured_store_details(): void
    {
        config([
            'store.name' => 'Corner Store',
            'store.address' => '1 High Street',
            'store.phone' => '0123 456789',
        ]);

        $order = Order::factory()->create();

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('config.store.name', 'Corner Store')
                ->where('config.store.address', '1 High Street')
                ->where('config.store.phone', '0123 456789'));
    }

    public function test_blank_store_details_fall_back_rather_than_render_empty(): void
    {
        // A key present but blank in .env arrives as an empty string.
        putenv('STORE_NAME=');
        putenv('STORE_ADDRESS=');
        $_ENV['STORE_NAME'] = '';
        $_ENV['STORE_ADDRESS'] = '';

        $config = require config_path('store.php');

        $this->assertSame(config('app.name'), $config['name']);
        $this->assertNull($config['address']);
    }
}
