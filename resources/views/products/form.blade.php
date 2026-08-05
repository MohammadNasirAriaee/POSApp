@csrf

<div class="space-y-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1" for="name">Product Name <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="sku">SKU / Barcode <span class="text-rose-500">*</span></label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" class="w-full font-mono rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('sku')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="category_id">Category</label>
            <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">Select Category...</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="price">Selling Price ($) <span class="text-rose-500">*</span></label>
            <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price ?? '0.00') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('price')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="cost">Unit Cost ($) <span class="text-rose-500">*</span></label>
            <input id="cost" name="cost" type="number" step="0.01" value="{{ old('cost', $product->cost ?? '0.00') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('cost')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="stock_quantity">Stock Quantity <span class="text-rose-500">*</span></label>
            <input id="stock_quantity" name="stock_quantity" type="number" value="{{ old('stock_quantity', $product->stock_quantity ?? '0') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            @error('stock_quantity')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="status">Status</label>
            <select id="status" name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="active" {{ old('status', $product->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $product->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1" for="description">Description (Optional)</label>
            <textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
