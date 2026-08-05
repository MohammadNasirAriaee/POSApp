@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Back to Orders</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Order Details Main Column -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900">Line Items</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-6">Product</th>
                            <th class="py-3 px-6 text-right">Price</th>
                            <th class="py-3 px-6 text-center">Qty</th>
                            <th class="py-3 px-6 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-3 px-6">
                                    <span class="font-medium text-slate-900">{{ $item->name }}</span>
                                </td>
                                <td class="py-3 px-6 text-right text-slate-600">${{ number_format($item->price, 2) }}</td>
                                <td class="py-3 px-6 text-center text-slate-600">x{{ $item->quantity }}</td>
                                <td class="py-3 px-6 text-right font-medium text-slate-900">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/50 border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3 px-6 text-right font-medium text-slate-600">Subtotal</td>
                            <td class="py-3 px-6 text-right font-bold text-slate-900">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-2 px-6 text-right font-medium text-slate-600">Tax</td>
                            <td class="py-2 px-6 text-right text-slate-900">${{ number_format($order->tax, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                            <tr>
                                <td colspan="3" class="py-2 px-6 text-right font-medium text-slate-600">Discount</td>
                                <td class="py-2 px-6 text-right text-emerald-600">-${{ number_format($order->discount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="border-t border-slate-200/80">
                            <td colspan="3" class="py-4 px-6 text-right font-bold text-slate-900 text-lg">Total</td>
                            <td class="py-4 px-6 text-right font-bold text-indigo-600 text-xl">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Sidebar Info Column -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Order Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Date</span>
                        <span class="font-medium text-slate-900">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Method</span>
                        <span class="font-medium text-slate-900">{{ $order->payment_method ?: 'Cash' }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-3">
                        <span class="text-slate-500">Cashier</span>
                        <span class="font-medium text-slate-900">{{ $order->employee ? $order->employee->name : 'Admin' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Customer</h3>
                @if ($order->customer)
                    <div class="space-y-1">
                        <p class="font-bold text-slate-900">{{ $order->customer->name }}</p>
                        @if($order->customer->email)
                            <p class="text-sm text-slate-500">{{ $order->customer->email }}</p>
                        @endif
                        @if($order->customer->phone)
                            <p class="text-sm text-slate-500">{{ $order->customer->phone }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Walk-in Customer</p>
                @endif
            </div>

            <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Receipt
            </button>

        </div>

    </div>
</div>
@endsection
