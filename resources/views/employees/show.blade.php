@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Top Back Navigation -->
        <div>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Employee Directory
            </a>
        </div>

        <!-- Employee Profile Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs relative overflow-hidden">
            <!-- Decorative Subtle Accent Gradient -->
            <div class="absolute top-0 right-0 h-32 w-32 bg-gradient-to-bl from-indigo-500/10 via-violet-500/5 to-transparent rounded-bl-full pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
                <!-- Avatar & Identity -->
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-bold text-xl sm:text-2xl flex items-center justify-center shadow-lg shadow-indigo-500/25 shrink-0">
                        {{ $employee->initials }}
                    </div>

                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $employee->name }}</h1>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $employee->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $employee->status)) }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-slate-500 mt-1 flex items-center gap-2">
                            <span>{{ $employee->position }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="text-xs text-slate-400">Tenure: {{ $employee->tenure }}</span>
                        </p>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-2 sm:self-start">
                    <!-- Quick Toggle Status -->
                    <form action="{{ route('employees.toggle-status', $employee) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Cycle Employment Status" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-semibold text-slate-700 transition-all">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Toggle Status
                        </button>
                    </form>

                    <!-- Edit Profile -->
                    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow-sm shadow-indigo-600/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>

                    <!-- Delete Employee -->
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($employee->name) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-all" title="Delete Employee">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI / Highlight Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Monthly Salary Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Monthly Compensation</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $employee->formatted_salary }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Base Monthly Salary</p>
            </div>

            <!-- Hire Date Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Date of Joining</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">
                    {{ optional($employee->hire_date)->format('M d, Y') ?? 'N/A' }}
                </p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $employee->tenure }} in service</p>
            </div>

            <!-- System Role Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Role Designation</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 truncate">{{ $employee->position }}</p>
                <p class="text-xs text-slate-500 mt-0.5">POS Permission Tier</p>
            </div>
        </div>

        <!-- Detailed Information Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contact Information Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contact Details
                </h3>

                <div class="space-y-3">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Email Address</span>
                        <a href="mailto:{{ $employee->email }}" class="text-sm font-semibold text-indigo-600 hover:underline">
                            {{ $employee->email }}
                        </a>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Phone Number</span>
                        <p class="text-sm font-medium text-slate-800">
                            {{ $employee->phone ?: 'Not provided' }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Residential Address</span>
                        <p class="text-sm font-medium text-slate-800 whitespace-pre-line">
                            {{ $employee->address ?: 'No address recorded' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Employment Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    System Record Details
                </h3>

                <div class="space-y-3">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Employee ID</span>
                        <p class="text-sm font-mono font-medium text-slate-800">
                            EMP-{{ str_pad($employee->id, 5, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Record Created</span>
                        <p class="text-sm font-medium text-slate-800">
                            {{ $employee->created_at ? $employee->created_at->format('M d, Y h:i A') : 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400">Last Updated</span>
                        <p class="text-sm font-medium text-slate-800">
                            {{ $employee->updated_at ? $employee->updated_at->diffForHumans() : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
