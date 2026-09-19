<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::withCount('resources')->orderBy('sort_order')->get()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:80',
            'name_ar' => 'nullable|max:80',
            'description' => 'nullable|max:500',
            'description_ar' => 'nullable|max:500',
            'icon' => 'nullable|max:80',
            'color' => 'nullable|max:30',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);
        $d['slug'] = Str::slug($d['name']);
        Category::create($d);
        return back()->with('success', 'Category added.');
    }

    public function update(Request $r, Category $category)
    {
        $d = $r->validate([
            'name' => 'required|max:80',
            'name_ar' => 'nullable|max:80',
            'description' => 'nullable|max:500',
            'description_ar' => 'nullable|max:500',
            'icon' => 'nullable|max:80',
            'color' => 'nullable|max:30',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);
        $d['slug'] = Str::slug($d['name']);
        $category->update($d);
        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->resources()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category with resources.']);
        }
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
