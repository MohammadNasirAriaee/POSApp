@extends('layouts.app')

@section('content')
    <div class="rounded border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">Add Employee</h1>
            <a href="{{ route('employees.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to list</a>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
            @include('employees.form')
            <div>
                <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white hover:bg-slate-700">Save Employee</button>
            </div>
        </form>
    </div>
@endsection
