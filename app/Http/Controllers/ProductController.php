<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

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

    public function store(Request $request) // function to store a new product in the database
    {
        $data = $request->validate([ // validate the request data
            'category_id' => 'nullable|exists:categories,id', // category_id is optional and must exist in the categories table
            'name' => 'required|string|max:255', // name is required, must be a string, and cannot exceed 255 characters
            'sku' => 'required|string|max:100|unique:products', // SKU is required, must be a string, cannot exceed 100 characters, and must be unique in the products table
            'price' => 'required|numeric|min:0', // price is required, must be numeric, and cannot be negative
            'cost' => 'nullable|numeric|min:0', // cost is optional, must be numeric, and cannot be negative
            'stock_quantity' => 'required|integer|min:0', // stock_quantity is required, must be an integer, and cannot be negative
            'status' => 'required|in:active,draft,out_of_stock', // status is required and must be one of the specified values
        ]);

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
        return Inertia::render('Products/Edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,draft,out_of_stock',
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
