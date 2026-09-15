<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_categories_by_search_term(): void
    {
        $match = Category::factory()->create(['name' => 'Beverages', 'slug' => 'beverages']);
        Category::factory()->create(['name' => 'Bakery', 'slug' => 'bakery']);

        $this->get(route('categories.index', ['search' => 'Bever']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Categories/Index')
                ->where('search', 'Bever')
                ->has('categories.data', 1)
                ->where('categories.data.0.id', $match->id));
    }

    public function test_it_returns_every_category_without_a_search_term(): void
    {
        Category::factory()->count(3)->create();

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('categories.data', 3));
    }
}
