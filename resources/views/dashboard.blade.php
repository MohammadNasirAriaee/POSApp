@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Overview of your store's performance.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Open Register
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Sales</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($stats['today_sales'], 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Orders</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['today_orders']) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Products</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_products']) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Customers</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_customers']) }}</p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Recent Transactions</h3>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Order ID</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Cashier</th>
                        <th class="py-3.5 px-6">Total</th>
                        <th class="py-3.5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-medium text-slate-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-6 text-slate-500">{{ $order->created_at->format('M d, g:i A') }}</td>
                            <td class="py-4 px-6">{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</td>
                            <td class="py-4 px-6">{{ $order->employee ? $order->employee->name : 'Admin' }}</td>
                            <td class="py-4 px-6 font-semibold">${{ number_format($order->total, 2) }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-500">No recent orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
