<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\ToolAnalytics;
use App\Services\ServerCapabilityService;
use Illuminate\Http\Request;

class ToolApiController extends Controller
{
    /**
     * Get all active tools & categories for frontend.
     */
    public function index(Request $request)
    {
        $categories = ToolCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $toolsQuery = Tool::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $toolsQuery->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $toolsQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('name_ar', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%")
                    ->orWhere('description_ar', 'LIKE', "%{$q}%")
                    ->orWhere('keywords', 'LIKE', "%{$q}%");
            });
        }

        $tools = $toolsQuery->with('category')->get()->map(function ($tool) {
            $depAvailable = ServerCapabilityService::isDependencyAvailable($tool->required_dependency);
            $tool->is_available = $depAvailable;
            return $tool;
        });

        return response()->json([
            'categories' => $categories,
            'tools' => $tools,
        ]);
    }

    /**
     * Show single tool details by slug.
     */
    public function show(string $slug)
    {
        $tool = Tool::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        $tool->is_available = ServerCapabilityService::isDependencyAvailable($tool->required_dependency);

        // Fetch related templates if configured
        $relatedTemplates = [];
        if (!empty($tool->related_resource_categories)) {
            $relatedTemplates = \App\Models\Resource::whereIn('resource_type', $tool->related_resource_categories)
                ->where('is_published', true)
                ->limit(4)
                ->get();
        }

        return response()->json([
            'tool' => $tool,
            'related_templates' => $relatedTemplates,
        ]);
    }

    /**
     * Track tool event analytics safely.
     */
    public function track(Request $request, string $slug)
    {
        $tool = Tool::where('slug', $slug)->first();
        if (!$tool) {
            return response()->json(['ok' => false], 444);
        }

        $eventType = $request->input('event_type', 'page_view');
        $validEvents = [
            'page_view',
            'tool_started',
            'upload_started',
            'processing_success',
            'processing_failed',
            'result_downloaded',
            'related_template_clicked'
        ];

        if (!in_array($eventType, $validEvents)) {
            $eventType = 'page_view';
        }

        $rawIp = $request->ip();
        $ipHash = hash('sha256', $rawIp . config('app.key'));

        ToolAnalytics::create([
            'tool_id' => $tool->id,
            'event_type' => $eventType,
            'status' => $request->input('status', 'success'),
            'processing_time_ms' => $request->input('processing_time_ms'),
            'input_size_bytes' => $request->input('input_size_bytes'),
            'output_size_bytes' => $request->input('output_size_bytes'),
            'user_ip_hash' => $ipHash,
            'country' => $request->input('country', 'EG'),
            'device' => $request->header('User-Agent') && str_contains($request->header('User-Agent'), 'Mobile') ? 'Mobile' : 'Desktop',
            'user_agent' => substr($request->header('User-Agent', ''), 0, 255),
        ]);

        if (in_array($eventType, ['tool_started', 'processing_success', 'result_downloaded'])) {
            $tool->increment('usage_count');
        }

        return response()->json(['ok' => true]);
    }
}
