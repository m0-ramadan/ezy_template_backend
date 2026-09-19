<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MainCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['subcategories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function mainCategories()
    {
        $mainCategories = MainCategory::where('is_active', true)
            ->with(['categories' => function ($q) {
                $q->where('is_active', true)->with(['subcategories' => function ($sq) {
                    $sq->where('is_active', true)->orderBy('sort_order');
                }])->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json($mainCategories);
    }
}
