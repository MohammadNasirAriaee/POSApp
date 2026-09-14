<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shares_flash_messages_with_inertia_pages(): void
    {
        $this->post(route('categories.store'), [
            'name' => 'Beverages',
            'is_active' => true,
        ])->assertRedirect(route('categories.index'));

        $this->followingRedirects()
            ->get(route('categories.index'))
            ->assertInertia(fn ($page) => $page->where('flash.success', 'Category created successfully.'));
    }

    public function test_it_shares_the_app_name_with_inertia_pages(): void
    {
        config(['app.name' => 'Corner Store POS']);

        $this->get(route('categories.index'))
            ->assertInertia(fn ($page) => $page->where('config.appName', 'Corner Store POS'));
    }

    public function test_it_shares_an_error_flash_when_a_delete_is_blocked(): void
    {
        $category = Category::create(['name' => 'Snacks', 'slug' => 'snacks', 'is_active' => true]);
        $category->products()->create([
            'name' => 'Chips',
            'sku' => 'CHIP-001',
            'price' => 1.50,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        $this->delete(route('categories.destroy', $category));

        $this->get(route('categories.index'))
            ->assertInertia(fn ($page) => $page->where('flash.error', 'Cannot delete category with associated products.'));
    }
}
