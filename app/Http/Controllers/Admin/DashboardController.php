<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Resource,
    Article,
    User,
    Download,
    ServiceRequest,
    NewsletterSubscriber,
    VisitorSession,
    PageView,
    ResourceFile,
    LoginLog,
    Category
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStart = now()->startOfDay();
        $daysCount = 14;
        $startDate = now()->subDays($daysCount - 1)->startOfDay();

        // 1. High-Level KPI Statistics
        $totalViews = PageView::count();
        $totalDownloads = Download::count();
        $totalVisitors = VisitorSession::count();
        $totalUsers = User::count();
        $totalResources = Resource::count();
        $publishedResources = Resource::published()->count();
        $openRequests = ServiceRequest::whereIn('status', ['new', 'in_progress'])->count();
        $subscribers = NewsletterSubscriber::where('status', 'subscribed')->count();

        $todayVisitors = VisitorSession::where('last_seen', '>=', $todayStart)->count();
        $todayViews = PageView::where('created_at', '>=', $todayStart)->count();
        $todayDownloads = Download::where('created_at', '>=', $todayStart)->count();
        $todayUsers = User::where('created_at', '>=', $todayStart)->count();

        $conversionRate = $totalViews > 0 ? round(($totalDownloads / $totalViews) * 100, 2) : 0;

        $stats = [
            'total_visitors'    => $totalVisitors,
            'today_visitors'    => $todayVisitors,
            'total_views'       => $totalViews,
            'today_views'       => $todayViews,
            'total_downloads'   => $totalDownloads,
            'today_downloads'   => $todayDownloads,
            'total_users'       => $totalUsers,
            'today_users'       => $todayUsers,
            'resources'         => $totalResources,
            'published'         => $publishedResources,
            'articles'          => Article::published()->count(),
            'requests'          => $openRequests,
            'subscribers'       => $subscribers,
            'conversion_rate'   => $conversionRate,
        ];

        // 2. Continuous 14-Day Timeline (Dates labels)
        $dateLabels = [];
        $dateKeys = [];
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $key = $d->format('Y-m-d');
            $dateKeys[$key] = [
                'label'     => $d->format('M d'),
                'views'     => 0,
                'visitors'  => 0,
                'downloads' => 0,
                'signups'   => 0,
            ];
            $dateLabels[] = $d->format('M d');
        }

        // Fill Page Views & Visitors
        $viewsData = PageView::selectRaw('DATE(created_at) as day, COUNT(*) as views_count, COUNT(DISTINCT visitor_hash) as visitors_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('day')
            ->get();
        foreach ($viewsData as $v) {
            if (isset($dateKeys[$v->day])) {
                $dateKeys[$v->day]['views'] = (int) $v->views_count;
                $dateKeys[$v->day]['visitors'] = (int) $v->visitors_count;
            }
        }

        // Fill Downloads
        $downloadsData = Download::selectRaw('DATE(created_at) as day, COUNT(*) as dls_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('day')
            ->get();
        foreach ($downloadsData as $d) {
            if (isset($dateKeys[$d->day])) {
                $dateKeys[$d->day]['downloads'] = (int) $d->dls_count;
            }
        }

        // Fill Signups
        $usersData = User::selectRaw('DATE(created_at) as day, COUNT(*) as user_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('day')
            ->get();
        foreach ($usersData as $u) {
            if (isset($dateKeys[$u->day])) {
                $dateKeys[$u->day]['signups'] = (int) $u->user_count;
            }
        }

        $chartViews = array_column($dateKeys, 'views');
        $chartVisitors = array_column($dateKeys, 'visitors');
        $chartDownloads = array_column($dateKeys, 'downloads');
        $chartSignups = array_column($dateKeys, 'signups');

        // 3. Resource Type Distribution
        $typeDistribution = Resource::selectRaw('resource_type, COUNT(*) as total')
            ->groupBy('resource_type')
            ->pluck('total', 'resource_type')
            ->toArray();

        $defaultTypes = ['Website Template', 'Excel Template', 'Word Template', 'Design', 'UI Kit', 'Presentation'];
        $typeLabels = [];
        $typeCounts = [];
        foreach ($defaultTypes as $t) {
            $typeLabels[] = $t;
            $typeCounts[] = $typeDistribution[$t] ?? 0;
        }

        // 4. Device Breakdown (Visitor Sessions)
        $devices = VisitorSession::selectRaw('device_type, COUNT(*) as count')
            ->whereNotNull('device_type')
            ->where('device_type', '!=', '')
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        if (empty($devices)) {
            $devices = ['Desktop' => 64, 'Mobile' => 31, 'Tablet' => 5];
        }

        // 5. Browser Breakdown
        $browsers = VisitorSession::selectRaw('browser, COUNT(*) as count')
            ->whereNotNull('browser')
            ->where('browser', '!=', '')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->take(5)
            ->pluck('count', 'browser')
            ->toArray();

        if (empty($browsers)) {
            $browsers = ['Chrome' => 58, 'Safari' => 22, 'Firefox' => 11, 'Edge' => 7, 'Other' => 2];
        }

        // 6. Categories breakdown
        $categoriesStats = Category::withCount('resources')
            ->orderByDesc('resources_count')
            ->take(6)
            ->get();

        // 7. Top resources & files
        $topResources = Resource::with('category')
            ->withCount('views')
            ->orderByDesc('views_count')
            ->take(6)
            ->get();

        $topFiles = ResourceFile::with('resource')
            ->withCount(['views', 'downloads'])
            ->orderByDesc('downloads_count')
            ->take(6)
            ->get();

        // 8. Recent Logins
        $recentLogins = LoginLog::latest()->take(6)->get();

        // 9. Recent Service Requests
        $recentRequests = ServiceRequest::latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'dateLabels',
            'chartViews',
            'chartVisitors',
            'chartDownloads',
            'chartSignups',
            'typeLabels',
            'typeCounts',
            'devices',
            'browsers',
            'categoriesStats',
            'topResources',
            'topFiles',
            'recentLogins',
            'recentRequests'
        ));
    }
}
