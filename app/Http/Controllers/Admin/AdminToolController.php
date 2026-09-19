<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\ToolAnalytics;
use App\Services\ServerCapabilityService;
use Illuminate\Http\Request;

class AdminToolController extends Controller
{
    public function index(Request $request)
    {
        $capabilities = ServerCapabilityService::checkAll();
        $tools = Tool::with('category')->orderBy('sort_order')->get();
        $categories = ToolCategory::orderBy('sort_order')->get();

        $analytics = [
            'total_events' => ToolAnalytics::count(),
            'total_tool_uses' => ToolAnalytics::whereIn('event_type', ['tool_started', 'processing_success', 'result_downloaded'])->count(),
            'successful' => ToolAnalytics::where('status', 'success')->count(),
            'failed' => ToolAnalytics::where('status', 'failed')->count(),
        ];

        return view('admin.tools.index', compact('capabilities', 'tools', 'categories', 'analytics'));
    }

    public function toggle(Request $request, Tool $tool)
    {
        $tool->is_active = !$tool->is_active;
        $tool->save();
        return redirect()->back()->with('success', "Tool '{$tool->name}' status updated.");
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $tool->update($validated);
        return redirect()->back()->with('success', "Tool updated successfully.");
    }
}
