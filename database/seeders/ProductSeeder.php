<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            ['name' => 'Coca Cola 330ml', 'category' => 'Beverages', 'price' => 1.50, 'cost' => 0.80],
            ['name' => 'Pepsi 330ml', 'category' => 'Beverages', 'price' => 1.50, 'cost' => 0.80],
            ['name' => 'Lays Classic Chips', 'category' => 'Snacks', 'price' => 2.00, 'cost' => 1.10],
            ['name' => 'Doritos Nacho Cheese', 'category' => 'Snacks', 'price' => 2.00, 'cost' => 1.10],
            ['name' => 'Whole Milk 1L', 'category' => 'Dairy', 'price' => 1.80, 'cost' => 1.20],
            ['name' => 'Cheddar Cheese Block', 'category' => 'Dairy', 'price' => 4.50, 'cost' => 2.50],
            ['name' => 'White Bread Loaf', 'category' => 'Bakery', 'price' => 2.20, 'cost' => 1.00],
            ['name' => 'Croissant', 'category' => 'Bakery', 'price' => 1.50, 'cost' => 0.50],
            ['name' => 'Bananas (1kg)', 'category' => 'Produce', 'price' => 1.99, 'cost' => 0.90],
            ['name' => 'Apples (1kg)', 'category' => 'Produce', 'price' => 2.49, 'cost' => 1.20],
            ['name' => 'Ground Beef 500g', 'category' => 'Meat', 'price' => 6.99, 'cost' => 4.50],
            ['name' => 'Chicken Breast 500g', 'category' => 'Meat', 'price' => 5.99, 'cost' => 3.80],
            ['name' => 'Spaghetti Pasta 500g', 'category' => 'Pantry', 'price' => 1.20, 'cost' => 0.60],
            ['name' => 'Tomato Sauce 400g', 'category' => 'Pantry', 'price' => 1.80, 'cost' => 0.90],
            ['name' => 'Frozen Pizza', 'category' => 'Frozen Foods', 'price' => 4.99, 'cost' => 2.50],
            ['name' => 'Vanilla Ice Cream 1L', 'category' => 'Frozen Foods', 'price' => 5.50, 'cost' => 3.00],
        ];

        foreach ($products as $index => $prodData) {
            $category = $categories->where('name', $prodData['category'])->first();
            
            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $prodData['name'],
                    'sku' => 'SKU' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                    'price' => $prodData['price'],
                    'cost' => $prodData['cost'],
                    'stock_quantity' => rand(10, 100),
                    'status' => 'active',
                ]);
            }
        }
    }
}
