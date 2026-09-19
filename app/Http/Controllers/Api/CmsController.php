<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Resource;
use App\Models\Download;
use App\Models\PageView;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    public function realtimeStats(Request $request): JsonResponse
    {
        // Record page view for real visits / refresh
        $ip = $request->ip() ?? '';
        $ua = (string)$request->userAgent();
        $hash = hash('sha256', $ip . '|' . $ua . '|' . config('app.key'));
        $sid = $request->hasSession() ? $request->session()->getId() : null;

        PageView::create([
            'visitor_hash' => $hash,
            'session_id' => $sid,
            'user_id' => optional($request->user())->id,
            'path' => '/about',
            'method' => 'GET',
            'ip_address' => $ip,
            'user_agent' => $ua,
            'referer' => $request->headers->get('referer'),
            'created_at' => now(),
        ]);

        $downloads = max((int) Download::count(), (int) Resource::sum('downloads_count'));
        $visits = (int) PageView::count();
        $uniqueVisitors = (int) VisitorSession::count();
        $templates = (int) (Resource::published()->count() ?: Resource::count());
        $avgRating = round((float) (Resource::where('rating', '>', 0)->avg('rating') ?: 4.9), 1);

        return response()->json([
            'downloads' => $downloads,
            'visits' => $visits,
            'unique_visitors' => $uniqueVisitors,
            'templates' => $templates,
            'rating' => $avgRating,
        ]);
    }

    public function settings(): JsonResponse
    {
        return response()->json([
            'site_name' => Setting::get('site_name', 'EzyTemplate'),
            'site_tagline' => Setting::get('site_tagline', 'Beautiful Templates for Every Project'),
            'site_description' => Setting::get('site_description', ''),
            'contact_email' => Setting::get('contact_email', 'support@ezytemplate.com'),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'copyright_text' => Setting::get('copyright_text', '© 2026 EzyTemplate by Ezystore. All rights reserved.'),
            'footer_subtext' => Setting::get('footer_subtext', 'Build • Create • Share • Grow'),
            'social_links' => Setting::get('social_links', []),
            'header_nav_links' => Setting::get('header_nav_links', []),
            'footer_columns' => Setting::get('footer_columns', []),
        ]);
    }

    public function home(): JsonResponse
    {
        return response()->json([
            'hero' => Setting::get('home_hero', []),
            'popular_tags' => Setting::get('home_popular_tags', []),
            'featured_section' => Setting::get('home_featured_section', []),
            'benefits' => Setting::get('home_benefits', []),
            'cta' => Setting::get('home_cta', []),
        ]);
    }

    public function about(): JsonResponse
    {
        $downloads = max((int) Download::count(), (int) Resource::sum('downloads_count'));
        $visits = (int) PageView::count();
        $templates = (int) (Resource::published()->count() ?: Resource::count());
        $avgRating = round((float) (Resource::where('rating', '>', 0)->avg('rating') ?: 4.9), 1);

        $liveStats = [
            [
                'icon' => 'Download',
                'count' => number_format($downloads) . '+',
                'raw_count' => $downloads,
                'label' => 'Total Downloads',
                'label_ar' => 'إجمالي التحميلات',
            ],
            [
                'icon' => 'Eye',
                'count' => number_format($visits) . '+',
                'raw_count' => $visits,
                'label' => 'Total Visits',
                'label_ar' => 'إجمالي الزيارات',
            ],
            [
                'icon' => 'FileCode2',
                'count' => number_format($templates) . '+',
                'raw_count' => $templates,
                'label' => 'Active Templates',
                'label_ar' => 'قالب متاح',
            ],
            [
                'icon' => 'Star',
                'count' => $avgRating . ' / 5.0',
                'raw_count' => $avgRating,
                'label' => 'Average Rating',
                'label_ar' => 'متوسط التقييمات',
            ],
        ];

        return response()->json([
            'hero' => Setting::get('about_hero', []),
            'stats' => $liveStats,
            'story' => Setting::get('about_story', []),
            'values' => Setting::get('about_values', []),
            'team' => Setting::get('about_team', []),
            'cta' => Setting::get('about_cta', []),
        ]);
    }

    public function services(): JsonResponse
    {
        return response()->json([
            'hero' => Setting::get('services_hero', []),
            'trust_badges' => Setting::get('services_trust_badges', []),
            'services' => Setting::get('services_list', []),
            'why_us' => Setting::get('services_why_us', []),
            'process' => Setting::get('services_process', []),
            'faqs' => Setting::get('services_faqs', []),
            'help_box' => Setting::get('services_help_box', []),
        ]);
    }

    public function customPages(): JsonResponse
    {
        return response()->json([
            'faq' => Setting::get('page_faq', []),
            'custom_requests' => Setting::get('page_custom_requests', []),
            'support' => Setting::get('page_support', []),
            'contact' => Setting::get('page_contact', []),
            'privacy' => Setting::get('page_privacy', []),
            'terms' => Setting::get('page_terms', []),
        ]);
    }

    public function customPageByKey(string $key): JsonResponse
    {
        $allowedKeys = ['faq', 'custom_requests', 'support', 'contact', 'privacy', 'terms'];
        if (!in_array($key, $allowedKeys)) {
            return response()->json(['message' => 'Page not found'], 404);
        }
        return response()->json(Setting::get('page_' . $key, []));
    }
}
