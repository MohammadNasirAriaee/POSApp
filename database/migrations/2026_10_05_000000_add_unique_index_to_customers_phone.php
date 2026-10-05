<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every other column the app treats as unique (customers.email,
     * employees.email, products.sku, categories.slug) is backed by a real
     * database index, not just app-level validation - customers.phone was
     * the one exception, relying entirely on NormalizedPhoneUnique with no
     * database-level backstop against a race or a write that bypasses
     * validation (a seeder, tinker, a future import script).
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });
    }
};
