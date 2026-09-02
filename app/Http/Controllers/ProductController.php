<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();

        $query = Product::with('category')->latest(); // get the latest products with their categories

        if ($search) {
            $query->where(function ($q) use ($search) { // search for products by name or SKU
                $q->where('name', 'like', "%{$search}%") // search by name
                  ->orWhere('sku', 'like', "%{$search}%"); // search by SKU
            }); // one agent
        }

        $products = $query->paginate(10)->withQueryString(); // paginate the results and keep the query string for pagination links

        return Inertia::render('Products/Index', compact('products', 'search')); // return the products index view with the products and search query
    }

    public function create() // function to show the create product form
    {
        $categories = Category::where('is_active', true)->get();
        return Inertia::render('Products/Create', compact('categories')); // return the create product view with the active categories
    }

    public function store(StoreProductRequest $request) // function to store a new product in the database
    {
        $data = $request->validated();

        Product::create($data); // create a new // product with the validated data

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return Inertia::render('Products/Show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get(); // get all active categories to populate the category dropdown in the edit form
        return Inertia::render('Products/Edit', compact('product', 'categories')); // return the edit product view with the product and active categories
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
