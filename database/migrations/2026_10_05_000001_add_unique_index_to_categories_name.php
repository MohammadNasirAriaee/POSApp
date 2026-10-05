<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * categories.name is validated as unique (case-insensitively, via
     * CaseInsensitiveUnique) but had no database-level backstop - only its
     * derived slug column does. A write that bypasses the FormRequest
     * entirely had nothing stopping a duplicate name, same gap as
     * customers.phone fixed in the previous migration. This only catches an
     * exact-string duplicate (unlike the app-level case-insensitive check),
     * but that is still strictly better than no constraint at all.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
