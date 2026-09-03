<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller // controller for handling POS operations
{
    public function index(Request $request) // function to show the POS interface with products, categories, and customers
    {
        $categoryId = $request->integer('category_id');
        $search = $request->string('search')->trim()->value();
        $categories = Category::active()->orderBy('name')->get();

        $query = Product::active()
            ->inStock()
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->active());
            });

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get(['id', 'category_id', 'name', 'sku', 'price', 'stock_quantity']);
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return \Inertia\Inertia::render('POS/Index', compact('products', 'categories', 'customers')); // return the POS interface view with the products, categories, and customers
    } // end of index function

    public function checkout(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $cart = $data['cart'];

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

            $tax = round($subtotal * ($data['tax_rate'] / 100), 2);
            $discount = min((float) $data['discount'], $subtotal + $tax); // never discount below zero
            $total = $subtotal + $tax - $discount;

            $order = Order::create([
                'customer_id' => $data['customer_id'] ?? null,
                'employee_id' => null, // In future: map to the logged in employee
                'subtotal' => $subtotal, // total before tax and discount
                'tax' => $tax, // tax amount applied to the order
                'discount' => $discount, // discount amount applied to the order
                'total' => $total, // final total after tax and discount
                'payment_method' => $data['payment_method'], // payment method used for the order
                'status' => Order::STATUS_COMPLETED,
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
