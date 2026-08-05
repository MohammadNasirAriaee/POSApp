@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Add Category</h1>
        <a href="{{ route('categories.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Back to list</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <form action="{{ route('categories.store') }}" method="POST">
            @include('categories.form', ['category' => null])
            
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-medium text-slate-700">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
