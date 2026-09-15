<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductLowStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The in-memory helper and the query scope express the same rule, so any
     * drift between them should fail here.
     */
    public function test_the_helper_agrees_with_the_scope_for_every_case(): void
    {
        $cases = [
            [Product::STATUS_ACTIVE, 0],
            [Product::STATUS_ACTIVE, Product::LOW_STOCK_THRESHOLD],
            [Product::STATUS_ACTIVE, Product::LOW_STOCK_THRESHOLD + 1],
            [Product::STATUS_DRAFT, 0],
            [Product::STATUS_OUT_OF_STOCK, 1],
        ];

        foreach ($cases as [$status, $stock]) {
            $product = Product::factory()->create([
                'status' => $status,
                'stock_quantity' => $stock,
            ]);

            $inScope = Product::lowStock()->whereKey($product->id)->exists();

            $this->assertSame(
                $inScope,
                $product->isLowStock(),
                "Mismatch for status {$status} with stock {$stock}."
            );
        }
    }
}
