<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return \Inertia\Inertia::render('Categories/Index', compact('categories'));
    }

    public function create()
    {
        return \Inertia\Inertia::render('Categories/Create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        return \Inertia\Inertia::render('Categories/Edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category with associated products.');
        }
        
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
