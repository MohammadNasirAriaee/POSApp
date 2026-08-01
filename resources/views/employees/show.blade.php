@extends('layouts.app')

@section('content')
    <div class="rounded border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ $employee->name }}</h1>
                <p class="text-sm text-slate-600">{{ $employee->position }}</p>
            </div>
            <a href="{{ route('employees.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to list</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-slate-700">Email</p>
                <p class="mt-1 text-slate-900">{{ $employee->email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-700">Phone</p>
                <p class="mt-1 text-slate-900">{{ $employee->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-700">Hire date</p>
                <p class="mt-1 text-slate-900">{{ optional($employee->hire_date)->format('Y-m-d') ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-700">Status</p>
                <p class="mt-1 text-slate-900 capitalize">{{ $employee->status }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm font-medium text-slate-700">Address</p>
                <p class="mt-1 text-slate-900">{{ $employee->address ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
@endsection
