<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_listing_includes_cost_so_margin_can_be_shown(): void
    {
        // The products table now shows a Margin column computed client-side
        // from price and cost; that only works if cost actually reaches the
        // page instead of being select()-restricted out of the query.
        $product = Product::factory()->create(['price' => 10.00, 'cost' => 6.00]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('products.data.0.cost', '6.00')
                ->where('products.data.0.price', '10.00'));
    }

    public function test_it_shares_the_low_stock_threshold(): void
    {
        Product::factory()->create();

        $this->get(route('products.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Products/Index')
                ->where('lowStockThreshold', Product::LOW_STOCK_THRESHOLD));
    }

    public function test_every_product_form_receives_the_status_labels(): void
    {
        $product = Product::factory()->create();

        foreach ([
            route('products.index'),
            route('products.create'),
            route('products.edit', $product),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->where('statusLabels', Product::statusLabels()));
        }
    }

    public function test_statuses_stay_in_sync_with_the_label_map(): void
    {
        $this->assertSame(Product::statuses(), array_keys(Product::statusLabels()));
    }

    public function test_it_filters_by_search_and_status_together(): void
    {
        $match = Product::factory()->create(['name' => 'Widget Pro', 'status' => Product::STATUS_ACTIVE]);
        Product::factory()->create(['name' => 'Widget Pro', 'status' => Product::STATUS_DRAFT]);
        Product::factory()->create(['name' => 'Gadget', 'status' => Product::STATUS_ACTIVE]);

        $this->get(route('products.index', ['search' => 'Widget', 'status' => Product::STATUS_ACTIVE]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('products.data', 1)
                ->where('products.data.0.id', $match->id));
    }

    public function test_it_ignores_an_unknown_status(): void
    {
        Product::factory()->count(2)->create();

        $this->get(route('products.index', ['status' => 'bogus']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('status', null)->has('products.data', 2));
    }
}
