<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->trim()->value();

        $query = Order::with(['customer', 'employee'])->latest();

        if (in_array($status, ['pending', 'completed', 'cancelled'], true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $orders = $query->paginate(15)->withQueryString();

        return Inertia::render('Orders/Index', compact('orders', 'status'));
    }

    public function show(Order $order) // function to show order details
    {
        $order->load(['customer', 'employee', 'items.product']);
        return Inertia::render('Orders/Show', compact('order'));
    }

    public function destroy(Order $order) // function to cancel order and return stock
    {
        $cancelled = DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status === 'cancelled') {
                return false;
            }

            $order->update(['status' => 'cancelled']);

            // Return stock to inventory
            foreach ($order->items()->with('product')->get() as $item) {
                $item->product?->increment('stock_quantity', $item->quantity);
            }

            return true;
        });

        if (! $cancelled) {
            return redirect()->back()->with('error', 'Order is already cancelled.');
        }

        return redirect()->back()->with('success', 'Order cancelled and stock returned successfully.');
    }
}
