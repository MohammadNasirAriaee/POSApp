@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Customer: {{ $customer->name }}</h1>
        <a href="{{ route('customers.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Back to Customers</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @method('PUT')
            @include('customers.form', ['customer' => $customer])
            
            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700 shadow-md shadow-indigo-500/20">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
