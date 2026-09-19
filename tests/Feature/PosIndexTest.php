<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_echoes_the_applied_filters_back_to_the_page(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $match = Product::factory()->create([
            'name' => 'Cola 330ml',
            'category_id' => $category->id,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);
        Product::factory()->create([
            'name' => 'Bread',
            'category_id' => $category->id,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->get(route('pos.index', ['search' => 'Cola', 'category_id' => $category->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('POS/Index')
                ->where('search', 'Cola')
                ->where('categoryId', $category->id)
                ->has('products', 1)
                ->where('products.0.id', $match->id));
    }

    public function test_it_lists_only_sellable_products(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $sellable = Product::factory()->create(['stock_quantity' => 3, 'status' => Product::STATUS_ACTIVE]);
        Product::factory()->create(['stock_quantity' => 0, 'status' => Product::STATUS_ACTIVE]);
        Product::factory()->create(['stock_quantity' => 3, 'status' => Product::STATUS_DRAFT]);
        Product::factory()->create([
            'stock_quantity' => 3,
            'status' => Product::STATUS_ACTIVE,
            'category_id' => $inactiveCategory->id,
        ]);

        $this->get(route('pos.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('products', 1)
                ->where('products.0.id', $sellable->id)
                ->where('search', null)
                ->where('categoryId', null));
    }
}
