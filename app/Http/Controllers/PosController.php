<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller // controller for handling POS operations
{
    public function index(Request $request) // function to show the POS interface with products, categories, and customers
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::where('status', 'active');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('name')->get();
        $customers = Customer::orderBy('first_name')->get();

        return \Inertia\Inertia::render('POS/Index', compact('products', 'categories', 'customers')); // return the POS interface view with the products, categories, and customers
    } // end of index function

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|json',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|string',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount' => 'required|numeric|min:0',
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty!');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;

            // Calculate real subtotal from DB to prevent tampering
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }

                $subtotal += $product->price * $item['quantity'];
            }

            $tax = $subtotal * ($request->tax_rate / 100);
            $total = $subtotal + $tax - $request->discount; // asdf 

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'employee_id' => auth()->id() ?? null, // In future: map to logged in user
                'subtotal' => $subtotal, // in the one of them
                'tax' => $tax, // taxation
                'discount' => $request->discount, // discount
                'total' => max(0, $total), // total max
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']); // how to do it

                OrderItem::create([
                    'order_id' => $order->id, // asfd asdf asdf asdf
                    'product_id' => $product->id, // next field
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
}
