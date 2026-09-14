<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAlertPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_only_low_stock_products_and_shares_the_threshold(): void
    {
        $low = Product::factory()->create([
            'name' => 'Bread Loaf',
            'stock_quantity' => 3,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::factory()->create([
            'name' => 'Milk Carton',
            'stock_quantity' => 12,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->get(route('inventory-alerts.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('InventoryAlerts/Index')
                ->where('lowStockThreshold', Product::LOW_STOCK_THRESHOLD)
                ->has('alerts', 1)
                ->where('alerts.0.id', $low->id));
    }
}
