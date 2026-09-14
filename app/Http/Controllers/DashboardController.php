<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Order::completed()
            ->where('created_at', '>=', now()->startOfDay())
            ->selectRaw('coalesce(sum(total), 0) as sales, count(*) as orders')
            ->first();

        $stats = [
            'today_sales' => (float) $today->sales,
            'today_orders' => (int) $today->orders,
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'low_stock_products' => Product::lowStock()->count(),
        ];

        $recentOrders = Order::with(['customer', 'employee'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
