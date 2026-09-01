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
            // Collapse duplicate cart lines and lock the rows so two concurrent
            // sales cannot both pass the stock check on the same product.
            $quantities = [];
            foreach ($cart as $item) {
                $id = (int) ($item['id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);

                if ($id <= 0 || $qty <= 0) {
                    throw new \Exception('Invalid cart line.');
                }

                $quantities[$id] = ($quantities[$id] ?? 0) + $qty;
            }

            $products = Product::whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;

            // Price and stock always come from the DB, never from the cart payload.
            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product || $product->status !== 'active') {
                    throw new \Exception('Product is no longer available.');
                }

                if ($product->stock_quantity < $quantity) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }

                $subtotal += $product->price * $quantity;
            }

            $tax = round($subtotal * ($request->tax_rate / 100), 2);
            $discount = min((float) $request->discount, $subtotal + $tax); // never discount below zero
            $total = $subtotal + $tax - $discount;

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'employee_id' => null, // In future: map to the logged in employee
                'subtotal' => $subtotal, // total before tax and discount
                'tax' => $tax, // tax amount applied to the order
                'discount' => $discount, // discount amount applied to the order
                'total' => $total, // final total after tax and discount
                'payment_method' => $request->payment_method, // payment method used for the order
                'status' => 'completed', // status of the order, set to completed for successful checkout
            ]);

            foreach ($quantities as $productId => $quantity) { //
                $product = $products->get($productId); // asdf

                OrderItem::create([
                    'order_id' => $order->id, // associate the order item with the created order
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ]);

                $product->decrement('stock_quantity', $quantity);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    } // end of checkout function
} // end of PosController class
