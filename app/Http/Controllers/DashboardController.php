<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $stats = [
            'today_sales' => \App\Models\Order::where('created_at', '>=', $today)->sum('total'),
            'today_orders' => \App\Models\Order::where('created_at', '>=', $today)->count(),
            'total_products' => \App\Models\Product::count(),
            'total_customers' => \App\Models\Customer::count(),
        ];

        $recentOrders = \App\Models\Order::with(['customer', 'employee'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentOrders'));
    }
