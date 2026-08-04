@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Dashboard Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Employee Directory</h1>
                <p class="text-sm text-slate-500 mt-1">Manage store staff, roles, status, and payroll overview.</p>
            </div>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Employee
            </a>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Employees -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Staff</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total'] ?? 0) }}</p>
                    <span class="text-xs text-slate-500 font-medium">Store Team Members</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            
            <!-- Active Staff -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Staff</p>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['active'] ?? 0) }}</p>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>
                    <span class="text-xs text-emerald-600 font-medium">Currently On Shift/Active</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Staff On Leave -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">On Leave</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['on_leave'] ?? 0) }}</p>
                    <span class="text-xs text-amber-600 font-medium">Temporary Absence</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Monthly Payroll -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Payroll</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($stats['monthly_payroll'] ?? 0, 2) }}</p>
                    <span class="text-xs text-slate-500 font-medium">Monthly Active Salary</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, phone or role..." class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                </div>

                <div class="flex flex-wrap sm:flex-nowrap gap-3">
                    <!-- Position Filter -->
                    <select name="position" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="">All Roles</option>
                        @foreach ($positions as $pos)
                            <option value="{{ $pos }}" {{ ($filters['position'] ?? '') === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="w-full sm:w-40 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="">All Statuses</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ ($filters['status'] ?? '') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-all">
                        Filter
                    </button>

                    @if (!empty($filters['search']) || !empty($filters['position']) || !empty($filters['status']))
                        <a href="{{ route('employees.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Main Employee Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            @if ($employees->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No employees found</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        @if(!empty($filters['search']) || !empty($filters['position']) || !empty($filters['status']))
                            No staff members match your selected search or filter criteria.
                        @else
                            There are currently no staff records in the system. Add your first employee to get started.
                        @endif
                    </p>
                    <div class="mt-6 flex items-center justify-center gap-3">
                        @if(!empty($filters['search']) || !empty($filters['position']) || !empty($filters['status']))
                            <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('employees.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700">
                            Add Employee
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-6">Employee</th>
                                <th class="py-3.5 px-6">Contact Info</th>
                                <th class="py-3.5 px-6">Role / Position</th>
                                <th class="py-3.5 px-6">Monthly Salary</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($employees as $employee)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <!-- Employee Avatar & Name -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-bold text-sm flex items-center justify-center shadow-xs shrink-0">
                                                {{ $employee->initials }}
                                            </div>
                                            <div>
                                                <a href="{{ route('employees.show', $employee) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block">
                                                    {{ $employee->name }}
                                                </a>
                                                <span class="text-xs text-slate-400 font-medium">
                                                    Tenure: {{ $employee->tenure }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Contact Info -->
                                    <td class="py-4 px-6">
                                        <div class="text-xs space-y-0.5">
                                            <a href="mailto:{{ $employee->email }}" class="text-slate-700 hover:text-indigo-600 font-medium block">
                                                {{ $employee->email }}
                                            </a>
                                            <span class="text-slate-400 block">
                                                {{ $employee->phone ?: 'No phone' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Position -->
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                            {{ $employee->position }}
                                        </span>
                                    </td>

                                    <!-- Salary -->
                                    <td class="py-4 px-6 font-semibold text-slate-900">
                                        {{ $employee->formatted_salary }}
                                    </td>

                                    <!-- Status (with quick toggle form) -->
                                    <td class="py-4 px-6">
                                        <form action="{{ route('employees.toggle-status', $employee) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Click to cycle status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $employee->status_badge_class }} hover:opacity-80 transition-opacity">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $employee->status === 'active' ? 'bg-emerald-500' : ($employee->status === 'on_leave' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                                {{ ucfirst(str_replace('_', ' ', $employee->status)) }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('employees.show', $employee) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="View Profile">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('employees.edit', $employee) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Edit Employee">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($employee->name) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Delete Employee">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if ($employees->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $employees->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
