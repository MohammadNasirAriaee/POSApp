@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Orders</h1>
            <p class="text-sm text-slate-500 mt-1">View transaction history and manage refunds.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            New Sale (POS)
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="flex gap-3">
                <select name="status" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled / Refunded</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                    Filter
                </button>

                @if (request('status'))
                    <a href="{{ route('orders.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($orders->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <p>No orders found matching your criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6">Order ID & Date</th>
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-6">Cashier</th>
                            <th class="py-3.5 px-6">Total</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-700">
                                    {{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $order->employee ? $order->employee->name : 'Admin' }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-emerald-50 text-emerald-700',
                                            'pending' => 'bg-amber-50 text-amber-700',
                                            'cancelled' => 'bg-rose-50 text-rose-700',
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View Receipt</a>
                                        @if ($order->status !== 'cancelled')
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this order? Stock will be returned.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 text-sm font-medium">Cancel / Refund</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
