<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Order::with(['customer', 'employee'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString(); // paginate 15 orders per page and keep the query string for pagination links

        return Inertia::render('Orders/Index', compact('orders', 'status'));
    }

    public function show(Order $order) // function to show order details
    {
        $order->load(['customer', 'employee', 'items.product']);
        return Inertia::render('Orders/Show', compact('order')); // return the show with the order data
    }

    public function destroy(Order $order) // function to cancel order and return stock
    {
        // Cancel order and return stock
        if ($order->status !== 'cancelled') {
            $order->update(['status' => 'cancelled']);

            // Return stock to inventory
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            return redirect()->back()->with('success', 'Order cancelled and stock returned successfully.');
        }

        return redirect()->back()->with('error', 'Order is already cancelled.');
    }
}
