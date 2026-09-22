<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_product(): void
    {
        $category = Category::factory()->create();

        $this->post(route('products.store'), [
            'name' => 'Widget',
            'sku' => 'WID-001',
            'category_id' => $category->id,
            'price' => 9.99,
            'cost' => 4.00,
            'stock_quantity' => 12,
            'status' => Product::STATUS_ACTIVE,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'WID-001', 'name' => 'Widget']);
    }

    public function test_it_stores_the_description(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Widget',
            'sku' => 'WID-DESC',
            'description' => 'A very useful widget.',
            'price' => 1.00,
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ])->assertRedirect(route('products.index'));

        $this->assertSame('A very useful widget.', Product::firstOrFail()->description);
    }

    public function test_it_updates_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old', 'status' => Product::STATUS_DRAFT]);

        $this->put(route('products.update', $product), [
            'name' => 'New',
            'sku' => $product->sku,
            'category_id' => null,
            'price' => 1.00,
            'cost' => 0.50,
            'stock_quantity' => 3,
            'status' => Product::STATUS_ACTIVE,
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertSame('New', $product->name);
        $this->assertSame(Product::STATUS_ACTIVE, $product->status);
    }

    public function test_editing_a_product_still_offers_its_own_deactivated_category(): void
    {
        // Deactivating a category doesn't strip it from products already
        // assigned to it, so the edit form must still be able to show it -
        // Category::active() alone would silently drop it from the options.
        $category = Category::factory()->create(['is_active' => false]);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('products.edit', $product))->assertOk();

        $categoryIds = collect($response->viewData('page')['props']['categories'])->pluck('id');

        $this->assertTrue($categoryIds->contains($category->id));
    }

    public function test_editing_a_product_with_no_category_does_not_error(): void
    {
        $product = Product::factory()->create(['category_id' => null]);

        $this->get(route('products.edit', $product))->assertOk();
    }

    public function test_it_rejects_a_duplicate_sku(): void
    {
        Product::factory()->create(['sku' => 'DUP-1']);

        $this->post(route('products.store'), [
            'name' => 'Other',
            'sku' => 'DUP-1',
            'price' => 1,
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ])->assertSessionHasErrors('sku');
    }

    public function test_skus_that_differ_only_by_case_are_rejected_as_duplicates(): void
    {
        // Stored already-uppercased, as any product created through this same
        // endpoint would be - matching how a real duplicate attempt looks.
        Product::factory()->create(['sku' => 'SKU-100']);

        $this->post(route('products.store'), [
            'name' => 'Other',
            'sku' => 'sku-100',
            'price' => 1,
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ])->assertSessionHasErrors('sku');

        $this->assertSame(1, Product::count());
    }

    public function test_a_new_products_sku_is_stored_uppercased(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Widget',
            'sku' => '  sku-200  ',
            'price' => 1,
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'SKU-200']);
    }

    public function test_updating_a_product_still_allows_keeping_its_own_sku(): void
    {
        $product = Product::factory()->create(['sku' => 'SKU-300']);

        $this->put(route('products.update', $product), [
            'name' => 'Renamed',
            'sku' => 'sku-300',
            'price' => 1,
            'stock_quantity' => 1,
            'status' => Product::STATUS_ACTIVE,
        ])->assertSessionHasNoErrors();

        $this->assertSame('SKU-300', $product->fresh()->sku);
    }

    public function test_it_refuses_to_delete_a_product_with_sales(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5, 'status' => Product::STATUS_ACTIVE]);

        $this->post(route('pos.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 1]],
            'customer_id' => null,
            'payment_method' => 'cash',
            'tax_rate' => 10,
            'discount' => 0,
        ]);

        $this->delete(route('products.destroy', $product))->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
