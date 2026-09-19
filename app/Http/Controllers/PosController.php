<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckoutException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index(Request $request)
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

        $products = $query->search($search)
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'sku', 'price', 'stock_quantity']);

        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        // The page filters the loaded set as the cashier types; these echo any
        // query string back so a linked-to filter is visible in the controls.
        return Inertia::render('POS/Index', [
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'search' => $search ?: null,
            'categoryId' => $categoryId > 0 ? $categoryId : null,
        ]);
    }

    public function checkout(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $cart = $data['cart'];

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty!');
        }

        try {
            $order = DB::transaction(function () use ($data, $cart) {
                // Collapse duplicate cart lines and lock the rows so two concurrent
                // sales cannot both pass the stock check on the same product.
                $quantities = [];
                foreach ($cart as $item) {
                    $id = (int) ($item['id'] ?? 0);
                    $qty = (int) ($item['quantity'] ?? 0);

                    if ($id <= 0 || $qty <= 0) {
                        throw new CheckoutException('Invalid cart line.');
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

                    if (! $product || $product->status !== Product::STATUS_ACTIVE) {
                        throw new CheckoutException('Product is no longer available.');
                    }

                    if ($product->stock_quantity < $quantity) {
                        throw new CheckoutException("Not enough stock for {$product->name}");
                    }

                    $subtotal += $product->price * $quantity;
                }

                $tax = round($subtotal * ($data['tax_rate'] / 100), 2);
                $discount = min((float) $data['discount'], $subtotal + $tax); // never discount below zero
                $total = $subtotal + $tax - $discount;

                // A cash drawer cannot hand back money it never received.
                $tendered = isset($data['tendered']) ? (float) $data['tendered'] : null;
                if ($tendered !== null && $tendered + 0.001 < $total) {
                    throw new CheckoutException('Tendered amount does not cover the total.');
                }

                $order = Order::create([
                    'customer_id' => $data['customer_id'] ?? null,
                    'employee_id' => null, // In future: map to the logged in employee
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => $total,
                    'tendered' => $tendered,
                    'payment_method' => $data['payment_method'],
                    'status' => Order::STATUS_COMPLETED,
                ]);

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get($productId);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity,
                    ]);

                    $product->decrement('stock_quantity', $quantity);
                }

                return $order;
            });
        } catch (CheckoutException $e) {
            return back()->with('error', 'Checkout failed: '.$e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Sale completed successfully!');
    }
}
