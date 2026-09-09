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
        Product::create([
            'name' => 'Bread Loaf',
            'sku' => 'BREAD-001',
            'price' => 2.50,
            'cost' => 1.20,
            'stock_quantity' => 3,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::create([
            'name' => 'Milk Carton',
            'sku' => 'MILK-001',
            'price' => 3.75,
            'cost' => 2.10,
            'stock_quantity' => 12,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $alerts = Product::query()
            ->active()
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->get();

        $this->assertCount(1, $alerts);
        $this->assertSame('Bread Loaf', $alerts->first()->name);
        $this->assertSame(3, $alerts->first()->stock_quantity);
    }
}
