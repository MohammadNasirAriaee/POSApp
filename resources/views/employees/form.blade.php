@php
    $statuses = ['active' => 'Active', 'inactive' => 'Inactive'];
@endphp

@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700" for="first_name">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required />
        @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="last_name">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required />
        @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $employee->email ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required />
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="phone">Phone</label>
        <input id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="position">Position</label>
        <input id="position" name="position" value="{{ old('position', $employee->position ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required />
        @error('position')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="salary">Salary</label>
        <input id="salary" name="salary" type="number" step="0.01" value="{{ old('salary', $employee->salary ?? '') }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
        @error('salary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="hire_date">Hire date</label>
        <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', optional($employee)->hire_date) }}" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
        @error('hire_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700" for="address">Address</label>
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">{{ old('address', $employee->address ?? '') }}</textarea>
        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="mt-1 block w-full rounded border-slate-300 bg-white shadow-sm focus:border-slate-500 focus:ring-slate-500">
            <option value="">Select status</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" {{ old('status', $employee->status ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
