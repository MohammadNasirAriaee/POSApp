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
