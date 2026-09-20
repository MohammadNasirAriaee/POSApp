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

        if (in_array($status, Order::statuses(), true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $orders = $query->paginate(15)->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'status' => $status,
            'statuses' => Order::statuses(),
        ]);
    }

    public function show(Order $order)
    {
        // The receipt only ever reads the name/price/quantity/subtotal
        // snapshotted on each order item at sale time - never the live
        // product - so there is no need to eager-load that relation here.
        $order->load(['customer', 'employee', 'items']);

        return Inertia::render('Orders/Show', compact('order'));
    }

    /**
     * Cancel an order and return its items to stock. The order itself is kept
     * for the sales record, so nothing is deleted.
     */
    public function cancel(Order $order)
    {
        $cancelled = DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status === Order::STATUS_CANCELLED) {
                return false;
            }

            // Only a completed sale took stock out, so only a completed sale
            // puts it back. Cancelling a pending order must not create stock.
            $returnsStock = $order->status === Order::STATUS_COMPLETED;

            $order->update(['status' => Order::STATUS_CANCELLED]);

            if ($returnsStock) {
                foreach ($order->items()->with('product')->get() as $item) {
                    $item->product?->increment('stock_quantity', $item->quantity);
                }
            }

            return true;
        });

        if (! $cancelled) {
            return redirect()->back()->with('error', 'Order is already cancelled.');
        }

        return redirect()->back()->with('success', 'Order cancelled and stock returned successfully.');
    }
}
