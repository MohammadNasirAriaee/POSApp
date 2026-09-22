<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $categoryId = $request->integer('category_id');

        $query = Product::with('category')->latest();

        if (in_array($status, Product::statuses(), true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        } else {
            $categoryId = null;
        }

        $query->search($search);

        $products = $query->paginate(10)->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
            'categories' => Category::orderByRaw('LOWER(name)')->get(['id', 'name']),
            'lowStockThreshold' => Product::LOW_STOCK_THRESHOLD,
            'statusLabels' => Product::statusLabels(),
        ]);
    }

    public function create()
    {
        $categories = Category::active()->orderByRaw('LOWER(name)')->get();

        return Inertia::render('Products/Create', [
            'categories' => $categories,
            'statusLabels' => Product::statusLabels(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product)
    {
        // A category can be deactivated after products were assigned to it;
        // Category::active() alone would then omit the product's own
        // category entirely, making the select look empty/unselected even
        // though the product is still assigned to it.
        $categories = Category::query()
            ->where('is_active', true)
            ->when(
                $product->category_id,
                fn ($query) => $query->orWhere('id', $product->category_id)
            )
            ->orderByRaw('LOWER(name)')
            ->get();

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'statusLabels' => Product::statusLabels(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return redirect()->route('products.index')->with('error', 'Cannot delete a product with recorded sales.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
