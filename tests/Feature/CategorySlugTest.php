<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_distinct_names_that_slug_alike_do_not_collide(): void
    {
        $this->post(route('categories.store'), ['name' => 'Foo Bar', 'is_active' => true])
            ->assertRedirect(route('categories.index'));

        $this->post(route('categories.store'), ['name' => 'Foo-Bar', 'is_active' => true])
            ->assertRedirect(route('categories.index'));

        $this->assertSame(
            ['foo-bar', 'foo-bar-2'],
            Category::orderBy('id')->pluck('slug')->all()
        );
    }

    public function test_updating_a_category_keeps_its_own_slug(): void
    {
        $category = Category::create(['name' => 'Drinks', 'slug' => 'drinks', 'is_active' => true]);

        $this->put(route('categories.update', $category), ['name' => 'Drinks', 'is_active' => true])
            ->assertRedirect(route('categories.index'));

        $this->assertSame('drinks', $category->fresh()->slug);
    }

    public function test_a_name_with_no_slug_characters_still_gets_a_slug(): void
    {
        $this->post(route('categories.store'), ['name' => '!!!', 'is_active' => true]);

        $this->assertSame('category', Category::first()->slug);
    }
}
