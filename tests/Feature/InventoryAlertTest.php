<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_returns_products_at_or_below_low_stock_threshold(): void
    {
        $atThreshold = Product::factory()->create([
            'name' => 'Bread Loaf',
            'stock_quantity' => Product::LOW_STOCK_THRESHOLD,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::factory()->create([
            'name' => 'Milk Carton',
            'stock_quantity' => Product::LOW_STOCK_THRESHOLD + 1,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $alerts = Product::lowStock()->get();

        $this->assertSame([$atThreshold->id], $alerts->pluck('id')->all());
    }

    public function test_it_ignores_products_that_are_not_active(): void
    {
        Product::factory()->create([
            'name' => 'Draft Item',
            'stock_quantity' => 0,
            'status' => Product::STATUS_DRAFT,
        ]);

        $this->assertCount(0, Product::lowStock()->get());
    }

    public function test_it_accepts_a_custom_threshold(): void
    {
        Product::factory()->create([
            'name' => 'Rice Bag',
            'stock_quantity' => 20,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->assertCount(0, Product::lowStock()->get());
        $this->assertCount(1, Product::lowStock(25)->get());
    }
}
