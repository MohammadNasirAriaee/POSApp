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
            $query->where(function ($q) use ($search) { // define a closure to filter the products
                $q->where('name', 'like', "%{$search}%") // search by name
                  ->orWhere('sku', 'like', "%{$search}%"); // 
            }); // one agent
        }

        $products = $query->paginate(10)->withQueryString();

        return Inertia::render('Products/Index', compact('products', 'search')); // asdf
    }

    public function create() // function to show the create product form
    {
        $categories = Category::where('is_active', true)->get(); // get all active categories to show in the create product form
        return Inertia::render('Products/Create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,draft,out_of_stock',
        ]);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
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
