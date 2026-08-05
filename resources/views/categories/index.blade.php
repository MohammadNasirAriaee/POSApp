@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Categories</h1>
            <p class="text-sm text-slate-500 mt-1">Manage product categories for your inventory.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </a>
    </div>

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium text-rose-900">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($categories->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-slate-500">No categories found. Create one to get started.</p>
            </div>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Name</th>
                        <th class="py-3.5 px-6">Products Count</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach ($categories as $category)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-medium text-slate-900">
                                {{ $category->name }}
                                @if($category->description)
                                    <span class="block text-xs font-normal text-slate-500 mt-0.5">{{ Str::limit($category->description, 50) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                    {{ $category->products_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
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
            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $categories->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
