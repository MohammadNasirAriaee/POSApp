@csrf

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="name">Category Name <span class="text-rose-500">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
        @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label for="is_active" class="text-sm text-slate-700 font-medium">Category is active</label>
    </div>
</div>
