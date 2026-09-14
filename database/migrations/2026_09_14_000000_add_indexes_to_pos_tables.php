<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index the columns the listings and KPI queries filter on: the low-stock
     * scope reads products by status + stock_quantity, the dashboard and order
     * list read orders by status + created_at, and the employee KPI query
     * groups by status.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'stock_quantity'], 'products_status_stock_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('status', 'employees_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_stock_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_at_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_status_index');
        });
    }
};
