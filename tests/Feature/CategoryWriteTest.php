<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_category(): void
    {
        $this->post(route('categories.store'), ['name' => 'Bakery', 'is_active' => true])
            ->assertRedirect(route('categories.index'));

        $category = Category::firstOrFail();
        $this->assertSame('Bakery', $category->name);
        $this->assertTrue($category->is_active);
    }

    public function test_an_unchecked_active_box_stores_false(): void
    {
        $this->post(route('categories.store'), ['name' => 'Hidden', 'is_active' => false]);

        $this->assertFalse(Category::firstOrFail()->is_active);
    }

    public function test_it_toggles_an_existing_category_inactive(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->put(route('categories.update', $category), [
            'name' => $category->name,
            'is_active' => false,
        ])->assertRedirect(route('categories.index'));

        $this->assertFalse($category->fresh()->is_active);
    }
}
