@php
    $statuses = \App\Models\Employee::statusLabels();

    // Both create() and edit() always pass 'positions'; this fallback just
    // keeps the partial safe to render if a future caller forgets to.
    $positionList = $positions ?? \App\Models\Employee::POSITIONS;
@endphp

@csrf

<div class="space-y-6">
    <!-- Section 1: Personal Details -->
    <div>
        <h3 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Personal Details
        </h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="first_name">
                    First Name <span class="text-rose-500">*</span>
                </label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $employee->first_name ?? '') }}" placeholder="e.g. Sarah" maxlength="100" autocomplete="given-name" autofocus aria-invalid="{{ $errors->has('first_name') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('first_name') ? 'first_name-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('first_name') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" required />
                @error('first_name')<p id="first_name-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="last_name">
                    Last Name <span class="text-rose-500">*</span>
                </label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $employee->last_name ?? '') }}" placeholder="e.g. Connor" maxlength="100" autocomplete="family-name" aria-invalid="{{ $errors->has('last_name') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('last_name') ? 'last_name-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('last_name') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" required />
                @error('last_name')<p id="last_name-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                    Email Address <span class="text-rose-500">*</span>
                </label>
                <input id="email" name="email" type="email" value="{{ old('email', $employee->email ?? '') }}" placeholder="sarah.c@company.com" maxlength="255" autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('email') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" required />
                @error('email')<p id="email-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="phone">
                    Phone Number
                </label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $employee->phone ?? '') }}" placeholder="+1 (555) 000-0000" maxlength="30" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('phone') ? 'phone-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('phone') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" />
                @error('phone')<p id="phone-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address">
                    Physical Address
                </label>
                <textarea id="address" name="address" rows="2" autocomplete="street-address" placeholder="Street, City, State, ZIP Code" aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('address') ? 'address-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('address') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}">{{ old('address', $employee->address ?? '') }}</textarea>
                @error('address')<p id="address-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <!-- Section 2: Position & Compensation -->
    <div class="pt-2">
        <h3 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Job & Compensation
        </h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="position">
                    Role / Position <span class="text-rose-500">*</span>
                </label>
                <select id="position" name="position" aria-invalid="{{ $errors->has('position') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('position') ? 'position-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('position') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" required>
                    <option value="">Select a Role</option>
                    @foreach ($positionList as $pos)
                        <option value="{{ $pos }}" {{ old('position', $employee->position ?? '') === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                    @endforeach
                </select>
                @error('position')<p id="position-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="salary">
                    Monthly Base Salary ($)
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-semibold text-sm">
                        $
                    </div>
                    <input id="salary" name="salary" type="number" step="0.01" min="0" inputmode="decimal" value="{{ old('salary', $employee->salary ?? '') }}" placeholder="3500.00" aria-invalid="{{ $errors->has('salary') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('salary') ? 'salary-error' : '' }}" class="w-full rounded-xl border bg-white pl-8 pr-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 {{ $errors->has('salary') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" />
                </div>
                @error('salary')<p id="salary-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="hire_date">
                    Hire Date
                </label>
                <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', $employee?->hire_date?->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" aria-invalid="{{ $errors->has('hire_date') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('hire_date') ? 'hire_date-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('hire_date') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}" />
                @error('hire_date')<p id="hire_date-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="status">
                    Employment Status
                </label>
                <select id="status" name="status" aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('status') ? 'status-error' : '' }}" class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 shadow-xs {{ $errors->has('status') ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/20' }}">
                    @foreach ($statuses as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $employee->status ?? \App\Models\Employee::STATUS_ACTIVE) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p id="status-error" class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>
