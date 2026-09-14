<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PosIndexMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hot_query_columns_are_indexed(): void
    {
        $this->assertTrue(Schema::hasIndex('products', 'products_status_stock_index'));
        $this->assertTrue(Schema::hasIndex('orders', 'orders_status_created_at_index'));
        $this->assertTrue(Schema::hasIndex('employees', 'employees_status_index'));
    }
}
