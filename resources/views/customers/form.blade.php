@csrf

<div class="space-y-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="first_name">First Name <span class="text-rose-500">*</span></label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $customer->first_name ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('first_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="last_name">Last Name</label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $customer->last_name ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
            @error('last_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="email">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="phone">Phone Number</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $customer->phone ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
            @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1" for="address">Mailing Address</label>
            <textarea id="address" name="address" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('address', $customer->address ?? '') }}</textarea>
            @error('address')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
