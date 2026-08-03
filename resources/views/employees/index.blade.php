@extends('layouts.app')

@section('content')
    <div class="rounded border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold">Employees</h1>
                <p class="text-sm text-slate-500">Manage employee records and contact information.</p>
            </div>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Add Employee</a>
        </div>

        @if ($employees->isEmpty())
            <div class="rounded border border-slate-200 bg-slate-50 p-6 text-slate-600">
                No employees found. Use the button above to add your first employee.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600 uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Position</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $employee->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $employee->email }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $employee->position }}</td>
                                <td class="px-4 py-3 text-slate-600 capitalize">{{ $employee->status }}</td>
                                <td class="px-4 py-3 space-x-2">
                                    <a href="{{ route('employees.show', $employee) }}" class="inline-flex rounded border border-slate-300 px-3 py-1 text-slate-700 hover:bg-slate-100">View</a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex rounded border border-amber-500 bg-amber-50 px-3 py-1 text-amber-700 hover:bg-amber-100">Edit</a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this employee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex rounded bg-red-600 px-3 py-1 text-white hover:bg-red-500">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
