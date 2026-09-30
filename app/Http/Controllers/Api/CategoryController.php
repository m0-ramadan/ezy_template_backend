<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MainCategory;

class CategoryController extends Controller
{
    /**
     * Only published, publicly reachable resources may be counted.
     * Draft / pending / archived / future-dated rows must never appear in a
     * public number.
     */
    private function publishedResources()
    {
        return fn($q) => $q->published();
    }

    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['subcategories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount(['resources as resources_count' => $this->publishedResources()])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['subcategories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount(['resources as resources_count' => $this->publishedResources()])
            ->firstOrFail();

        return response()->json($category);
    }

    public function mainCategories()
    {
        $mainCategories = MainCategory::where('is_active', true)
            ->with(['categories' => function ($q) {
                $q->where('is_active', true)->with(['subcategories' => function ($sq) {
                    $sq->where('is_active', true)->orderBy('sort_order');
                }])->orderBy('sort_order');
            }])
            ->withCount([
                'resources as resources_count' => $this->publishedResources(),
                'categories as categories_count' => fn($q) => $q->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json($mainCategories);
    }
}
