@php
    $statuses = [
        'active' => 'Active',
        'on_leave' => 'On Leave',
        'inactive' => 'Inactive',
    ];

    $positionList = $positions ?? \App\Models\Employee::POSITIONS;
@endphp

@csrf

<div class="space-y-6">
    <!-- Section 1: Personal Details -->
    <div>
        <h3 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Personal Details
        </h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="first_name">
                    First Name <span class="text-rose-500">*</span>
                </label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $employee->first_name ?? '') }}" placeholder="e.g. Sarah" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" required />
                @error('first_name')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="last_name">
                    Last Name <span class="text-rose-500">*</span>
                </label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $employee->last_name ?? '') }}" placeholder="e.g. Connor" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" required />
                @error('last_name')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                    Email Address <span class="text-rose-500">*</span>
                </label>
                <input id="email" name="email" type="email" value="{{ old('email', $employee->email ?? '') }}" placeholder="sarah.c@company.com" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" required />
                @error('email')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="phone">
                    Phone Number
                </label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $employee->phone ?? '') }}" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" />
                @error('phone')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address">
                    Physical Address
                </label>
                <textarea id="address" name="address" rows="2" placeholder="Street, City, State, ZIP Code" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs">{{ old('address', $employee->address ?? '') }}</textarea>
                @error('address')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <!-- Section 2: Position & Compensation -->
    <div class="pt-2">
        <h3 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Job & Compensation
        </h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="position">
                    Role / Position <span class="text-rose-500">*</span>
                </label>
                <select id="position" name="position" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" required>
                    <option value="">Select a Role</option>
                    @foreach ($positionList as $pos)
                        <option value="{{ $pos }}" {{ old('position', $employee->position ?? '') === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                    @endforeach
                </select>
                @error('position')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="salary">
                    Monthly Base Salary ($)
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-semibold text-sm">
                        $
                    </div>
                    <input id="salary" name="salary" type="number" step="0.01" min="0" value="{{ old('salary', $employee->salary ?? '') }}" placeholder="3500.00" class="w-full rounded-xl border border-slate-300 bg-white pl-8 pr-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" />
                </div>
                @error('salary')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="hire_date">
                    Hire Date
                </label>
                <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', optional($employee ?? null)->hire_date ? optional($employee->hire_date)->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs" />
                @error('hire_date')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="status">
                    Employment Status
                </label>
                <select id="status" name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs">
                    @foreach ($statuses as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $employee->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>
