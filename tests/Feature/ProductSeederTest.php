<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_fresh_seed_demonstrates_the_low_stock_feature(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        // A demo database that never produces a low-stock product would make
        // the Inventory Alerts page and the dashboard's low-stock count look
        // permanently empty, hiding a documented feature.
        $this->assertGreaterThan(0, Product::lowStock()->count());
        $this->assertGreaterThan(0, Product::where('stock_quantity', 0)->count());
    }

    public function test_every_seeded_product_uses_the_active_status_constant(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $this->assertSame(
            Product::count(),
            Product::where('status', Product::STATUS_ACTIVE)->count()
        );
    }
}
