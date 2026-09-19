<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $r)
    {
        $q = Resource::published()
            ->with(['category', 'subcategory', 'files', 'tags'])
            ->withCount('views');

        if ($r->filled('type')) {
            $type = strtolower((string) $r->type);
            if ($type === 'canva') {
                $q->where(function ($x) {
                    $x->whereNotNull('canva_design_id')
                        ->orWhereHas('category', fn($c) => $c->where('slug', 'like', '%canva%'));
                });
            } else {
                $q->where('resource_type', $type);
            }
        }
        if ($r->filled('category')) {
            $q->whereHas('category', fn($x) => $x->where('slug', $r->category));
        }
        if ($r->filled('subcategory')) {
            $q->whereHas('subcategory', fn($x) => $x->where('slug', $r->subcategory));
        }
        if ($r->filled('platform')) {
            $platform = strtolower((string) $r->platform);
            match ($platform) {
                'canva' => $q->whereNotNull('canva_design_id'),
                'excel', 'xlsx' => $q->where('resource_type', 'excel'),
                'word', 'docx' => $q->where('resource_type', 'word'),
                'powerpoint', 'pptx', 'presentation' => $q->where('resource_type', 'presentation'),
                default => null,
            };
        }
        if ($r->filled('search')) {
            $term = '%' . $r->search . '%';
            $q->where(function ($x) use ($term) {
                $x->where('title', 'like', $term)
                    ->orWhere('short_description', 'like', $term)
                    ->orWhere('title_ar', 'like', $term)
                    ->orWhere('short_description_ar', 'like', $term)
                    ->orWhere('seo_title', 'like', $term)
                    ->orWhere('seo_description', 'like', $term);
            });
        }
        if ($r->boolean('featured')) {
            $q->where('featured', true);
        }

        $sort = $r->get('sort', 'latest');
        $q = match ($sort) {
            'popular' => $q->orderByDesc('downloads_count'),
            'rating' => $q->orderByDesc('rating'),
            default => $q->orderByDesc('published_at'),
        };

        return response()->json($q->paginate((int) $r->get('per_page', 12)));
    }

    public function show(Request $request, string $slug)
    {
        $x = Resource::published()
            ->with([
                'category',
                'subcategory',
                'files',
                'tags',
                'reviews' => fn($q) => $q->where('status', 'approved')->latest(),
            ])
            ->withCount('views')
            ->where('slug', $slug)
            ->firstOrFail();

        try {
            $ip = $request->ip() ?? '';
            $ua = (string) $request->userAgent();
            $hash = hash('sha256', $ip . '|' . $ua . '|' . config('app.key'));
            \App\Models\ResourceView::create([
                'resource_id' => $x->id,
                'visitor_hash' => $hash,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'user_id' => optional($request->user())->id,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'referer' => $request->headers->get('referer'),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Analytics must never break the resource response.
        }

        return response()->json($x);
    }
}
