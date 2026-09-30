<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Resource;
use App\Models\Tool;

/**
 * Public, read-only catalogue statistics.
 *
 * Every number returned here is counted directly from the database using the
 * same "published and publicly reachable" rule that the public listing pages
 * use. Nothing is hard coded, estimated or cached from a constant.
 */
class CatalogStatsController extends Controller
{
    public function __invoke()
    {
        $published = fn($q) => $q->published();

        $totalTemplates = Resource::published()->count();

        $byType = Resource::published()
            ->selectRaw('resource_type, COUNT(*) as total')
            ->groupBy('resource_type')
            ->pluck('total', 'resource_type')
            ->all();

        $byMainCategory = MainCategory::query()
            ->where('is_active', true)
            ->withCount(['resources as total' => $published])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'slug' => $m->slug,
                'name' => $m->name,
                'name_ar' => $m->name_ar,
                'total' => (int) $m->total,
            ])
            ->values()
            ->all();

        $canva = Resource::published()
            ->where(function ($q) {
                $q->whereNotNull('canva_design_id')
                    ->orWhereHas('category', fn($c) => $c->where('slug', 'like', '%canva%'));
            })
            ->count();

        $totalTools = Tool::query()->where('is_active', 1)->count();

        $publishedArticles = Article::published()->count();

        $categoryCounts = Category::query()
            ->where('is_active', true)
            ->withCount(['resources as resources_count' => $published])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => [
                'slug' => $c->slug,
                'name' => $c->name,
                'name_ar' => $c->name_ar,
                'total' => (int) $c->resources_count,
            ])
            ->values()
            ->all();

        return response()->json([
            'total_templates' => $totalTemplates,
            'by_type' => array_map('intval', $byType),
            'canva_templates' => (int) $canva,
            'by_main_category' => $byMainCategory,
            'by_category' => $categoryCounts,
            'total_tools' => (int) $totalTools,
            'published_articles' => (int) $publishedArticles,
        ]);
    }
}
