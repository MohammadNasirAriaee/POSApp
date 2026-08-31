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
        $today = now()->startOfDay();

        // Cancelled orders are refunded/restocked, so they must not count as sales.
        $todayOrders = Order::where('created_at', '>=', $today)->where('status', '!=', 'cancelled');

        $stats = [
            'today_sales' => (clone $todayOrders)->sum('total'),
            'today_orders' => (clone $todayOrders)->count(),
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'low_stock_products' => Product::where('status', 'active')->where('stock_quantity', '<=', 5)->count(),
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
