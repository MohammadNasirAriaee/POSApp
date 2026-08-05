@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Inventory Products</h1>
            <p class="text-sm text-slate-500 mt-1">Manage items, pricing, and stock levels.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name or SKU (Barcode)..." class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2 text-sm text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
            </div>

            <div class="flex gap-3">
                <select name="category_id" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                    Filter
                </button>

                @if (request('search') || request('category_id'))
                    <a href="{{ route('products.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($products->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <p>No products found matching your criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6">Product Details</th>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Price</th>
                            <th class="py-3.5 px-6">Stock</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($products as $product)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono mt-0.5">SKU: {{ $product->sku }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        {{ $product->category ? $product->category->name : 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    ${{ number_format($product->price, 2) }}
                                </td>
                                <td class="py-4 px-6">
                                    @if ($product->stock_quantity <= 5)
                                        <span class="text-rose-600 font-bold">{{ $product->stock_quantity }} (Low)</span>
                                    @else
                                        <span class="text-slate-700 font-semibold">{{ $product->stock_quantity }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 text-sm font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
