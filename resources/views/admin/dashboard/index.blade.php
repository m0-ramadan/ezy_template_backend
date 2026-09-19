@extends('admin.layouts.app')

@section('title', 'Dashboard & Analytics — EzyTemplate Admin')
@section('section', 'EXECUTIVE OVERVIEW')
@section('heading', 'Platform Intelligence Center')

@section('content')
    <style>
        /* Executive Dashboard Custom Polish */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.08);
            border-color: #cbd5e1;
        }

        .kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .kpi-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .kpi-val {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .kpi-sub {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .kpi-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .kpi-badge.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .kpi-badge.green {
            background: #ecfdf5;
            color: #059669;
        }

        .kpi-badge.purple {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .kpi-badge.amber {
            background: #fffbeb;
            color: #d97706;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .chart-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px 24px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .panel-head h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-head .desc {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 280px;
        }

        .chart-container-sm {
            position: relative;
            width: 100%;
            height: 240px;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .table-modern th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-modern td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        .table-modern tr:hover td {
            background: #f8fafc;
        }

        .type-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: #f1f5f9;
            color: #334155;
        }

        .type-pill.web {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .type-pill.excel {
            background: #ecfdf5;
            color: #047857;
        }

        .type-pill.word {
            background: #f0f9ff;
            color: #0369a1;
        }

        .type-pill.design {
            background: #faf5ff;
            color: #6b21a8;
        }

        .welcome-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 18px;
            padding: 24px 30px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            position: relative;
            overflow: hidden;
        }

        .welcome-header::after {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .welcome-title h2 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
        }

        .welcome-title p {
            margin: 0;
            font-size: 13px;
            color: #94a3b8;
        }

        .welcome-actions {
            display: flex;
            gap: 10px;
            position: relative;
            z-index: 2;
        }
    </style>

    <!-- Welcome & Live Status Header -->
    <div class="welcome-header">
        <div class="welcome-title">
            <h2>Welcome back, {{ auth()->user()->name }} 👋</h2>
            <p>Live real-time operational metrics & performance intelligence for <b>EzyTemplate</b>.</p>
        </div>
        <div class="welcome-actions">
            <a class="btn" href="{{ route('admin.resources.create') }}"
                style="background:#2563eb; color:#fff; border:none; padding:10px 18px; border-radius:10px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <span>+</span> Add Resource
            </a>
            <a href="/" target="_blank"
                style="background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:10px 16px; border-radius:10px; font-weight:600; text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                View Site ↗
            </a>
        </div>
    </div>

    <!-- 6 High-Impact KPI Cards -->
    <div class="kpi-grid">
        <!-- Visitors -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">Unique Visitors</span>
                <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">👥</div>
            </div>
            <div class="kpi-val">{{ number_format($stats['total_visitors']) }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge blue">+{{ number_format($stats['today_visitors']) }} today</span>
                <span>Audience reached</span>
            </div>
        </div>

        <!-- Page Views -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">Total Page Views</span>
                <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">👀</div>
            </div>
            <div class="kpi-val">{{ number_format($stats['total_views']) }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge green">+{{ number_format($stats['today_views']) }} today</span>
                <span>Impressions</span>
            </div>
        </div>

        <!-- Total Downloads -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">Total Downloads</span>
                <div class="kpi-icon" style="background:#f5f3ff; color:#7c3aed;">📥</div>
            </div>
            <div class="kpi-val">{{ number_format($stats['total_downloads']) }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge purple">+{{ number_format($stats['today_downloads']) }} today</span>
                <span>Delivered assets</span>
            </div>
        </div>

        <!-- Published Resources -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">Catalog Inventory</span>
                <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">📦</div>
            </div>
            <div class="kpi-val">{{ number_format($stats['published']) }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge amber">{{ number_format($stats['resources']) }} total</span>
                <span>Active templates</span>
            </div>
        </div>

        <!-- User Signups -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">User Accounts</span>
                <div class="kpi-icon" style="background:#f0fdf4; color:#16a34a;">👤</div>
            </div>
            <div class="kpi-val">{{ number_format($stats['total_users']) }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge green">+{{ number_format($stats['today_users']) }} today</span>
                <span>Members</span>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-title">Conversion Rate</span>
                <div class="kpi-icon" style="background:#fdf2f8; color:#db2777;">⚡</div>
            </div>
            <div class="kpi-val">{{ $stats['conversion_rate'] }}%</div>
            <div class="kpi-sub">
                <span class="kpi-badge purple">DL / Views</span>
                <span>Download ratio</span>
            </div>
        </div>
    </div>

    <!-- Row 1 Charts: Traffic Overview & Daily Downloads -->
    <div class="chart-grid">
        <!-- Chart 1: Traffic Area Chart -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>📈 Traffic & Page Views (14 Days)</h3>
                    <span class="desc">Interactive view of page impressions vs unique visitors</span>
                </div>
                <a href="{{ route('admin.analytics.visitors') }}"
                    style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                    Detailed Analytics →
                </a>
            </div>
            <div class="chart-container">
                <canvas id="trafficChart"></canvas>
            </div>
        </section>

        <!-- Chart 2: Downloads Bar Chart -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>📥 Daily Downloads Trend</h3>
                    <span class="desc">Asset downloads activity over 14 days</span>
                </div>
                <a href="{{ route('admin.analytics.downloads') }}"
                    style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                    View All →
                </a>
            </div>
            <div class="chart-container">
                <canvas id="downloadsChart"></canvas>
            </div>
        </section>
    </div>

    <!-- Row 2 Charts: 3 Multi-Dimensional Breakdown Charts -->
    <div class="chart-grid-3">
        <!-- Chart 3: Resources & Downloads by Type -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>🎨 Catalog by Resource Type</h3>
                    <span class="desc">Distribution of templates & digital assets</span>
                </div>
            </div>
            <div class="chart-container-sm">
                <canvas id="typesChart"></canvas>
            </div>
        </section>

        <!-- Chart 4: Device Breakdown -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>📱 Visitor Devices</h3>
                    <span class="desc">Platform breakdown (Desktop vs Mobile vs Tablet)</span>
                </div>
            </div>
            <div class="chart-container-sm">
                <canvas id="devicesChart"></canvas>
            </div>
        </section>

        <!-- Chart 5: Browser Distribution -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>🌐 Browser Share</h3>
                    <span class="desc">Top web browsers used by audience</span>
                </div>
            </div>
            <div class="chart-container-sm">
                <canvas id="browsersChart"></canvas>
            </div>
        </section>
    </div>

    <!-- Row 3: Top Performance Tables & Security Logs -->
    <div class="chart-grid">
        <!-- Most Viewed Resources Table -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>🔥 Most Popular Resources</h3>
                    <span class="desc">Ranked by overall views and visitor interactions</span>
                </div>
                <a href="{{ route('admin.resources.index') }}"
                    style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                    Manage Catalog →
                </a>
            </div>
            <div class="table-wrap" style="overflow-x:auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Resource Title</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Views</th>
                            <th>Downloads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topResources as $res)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <img src="{{ $res->preview_image ? (str_starts_with($res->preview_image, 'http') ? $res->preview_image : '/storage/' . $res->preview_image) : '/assets/placeholder.png' }}"
                                            alt="{{ $res->title }}"
                                            style="width:36px; height:36px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0;">
                                        <div>
                                            <b
                                                style="color:#0f172a;">{{ \Illuminate\Support\Str::limit($res->title, 28) }}</b>
                                            <div style="font-size:11px; color:#64748b;">{{ $res->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="type-pill {{ str_contains(strtolower($res->resource_type), 'excel') ? 'excel' : (str_contains(strtolower($res->resource_type), 'word') ? 'word' : (str_contains(strtolower($res->resource_type), 'design') ? 'design' : 'web')) }}">
                                        {{ $res->resource_type }}
                                    </span>
                                </td>
                                <td>{{ $res->category ? $res->category->name : 'General' }}</td>
                                <td><b>{{ number_format($res->views_count) }}</b></td>
                                <td><span
                                        style="color:#059669; font-weight:700;">{{ number_format($res->downloads_count) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">No resource
                                    data available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Top Downloaded Files Table -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>⚡ Top Downloaded Files</h3>
                    <span class="desc">Direct file attachment download velocity</span>
                </div>
                <a href="{{ route('admin.analytics.files') }}"
                    style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                    File Stats →
                </a>
            </div>
            <div class="table-wrap" style="overflow-x:auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>File Asset</th>
                            <th>Size</th>
                            <th>Downloads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFiles as $file)
                            <tr>
                                <td>
                                    <div>
                                        <b
                                            style="color:#0f172a;">{{ \Illuminate\Support\Str::limit($file->original_name, 24) }}</b>
                                        <div style="font-size:11px; color:#64748b;">
                                            {{ $file->resource ? \Illuminate\Support\Str::limit($file->resource->title, 26) : 'Independent' }}
                                        </div>
                                    </div>
                                </td>
                                <td><span
                                        style="font-size:11px; color:#64748b;">{{ $file->file_size ? number_format($file->file_size / 1024 / 1024, 2) . ' MB' : '—' }}</span>
                                </td>
                                <td>
                                    <span class="kpi-badge green">
                                        {{ number_format($file->downloads_count) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:#94a3b8; padding:20px;">No files
                                    uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Row 4: Security & Recent Activities -->
    <div class="chart-grid">
        <!-- Login Security Logs -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>🛡️ Recent Login Activities</h3>
                    <span class="desc">Security audits and authentication attempts</span>
                </div>
                <a href="{{ route('admin.login-logs.index') }}"
                    style="font-size:12px; font-weight:700; color:#2563eb; text-decoration:none;">
                    Full Security Log →
                </a>
            </div>
            <div class="table-wrap" style="overflow-x:auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP & Location</th>
                            <th>Device / Agent</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogins as $log)
                            <tr>
                                <td>
                                    <b>{{ $log->email }}</b>
                                </td>
                                <td>
                                    <div>{{ $log->ip_address }}</div>
                                    <small
                                        style="color:#64748b;">{{ $log->city ? $log->city . ', ' : '' }}{{ $log->country ?? 'Global' }}</small>
                                </td>
                                <td>
                                    <span
                                        style="font-size:11px; color:#475569;">{{ \Illuminate\Support\Str::limit($log->user_agent, 24) }}</span>
                                </td>
                                <td>
                                    @if ($log->status === 'success')
                                        <span class="kpi-badge green">Success</span>
                                    @else
                                        <span class="kpi-badge amber">Failed</span>
                                    @endif
                                </td>
                                <td style="font-size:11px; color:#64748b;">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">No login logs
                                    recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Platform Health & Requests -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>📋 Quick System Status</h3>
                    <span class="desc">Service requests & content pipeline</span>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:14px;">
                <div
                    style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                    <div>
                        <b style="color:#0f172a; font-size:13px;">Open Service Requests</b>
                        <div style="font-size:11px; color:#64748b;">Custom template inquiries</div>
                    </div>
                    <span class="kpi-badge amber" style="font-size:13px; padding:4px 12px;">{{ $stats['requests'] }}
                        Pending</span>
                </div>

                <div
                    style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                    <div>
                        <b style="color:#0f172a; font-size:13px;">Newsletter Community</b>
                        <div style="font-size:11px; color:#64748b;">Active email subscribers</div>
                    </div>
                    <span class="kpi-badge blue"
                        style="font-size:13px; padding:4px 12px;">{{ number_format($stats['subscribers']) }}</span>
                </div>

                <div
                    style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                    <div>
                        <b style="color:#0f172a; font-size:13px;">Published Blog Articles</b>
                        <div style="font-size:11px; color:#64748b;">Guides & tutorials</div>
                    </div>
                    <span class="kpi-badge green"
                        style="font-size:13px; padding:4px 12px;">{{ number_format($stats['articles']) }}</span>
                </div>

                <div
                    style="margin-top:10px; padding:14px; background:linear-gradient(135deg, #eff6ff, #f5f3ff); border-radius:12px; border:1px solid #dbeafe;">
                    <div style="font-size:12px; font-weight:700; color:#1e40af; margin-bottom:4px;">🚀 Growth Tip</div>
                    <div style="font-size:12px; color:#3b82f6; line-height:1.5;">
                        Adding Excel & UI Kit templates regularly increases conversion rate by up to 35% on weekends.
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Initialize Chart.js Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = false;
            const textColor = '#64748b';
            const gridColor = '#f1f5f9';

            // Global Font Settings
            Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
            Chart.defaults.color = textColor;

            const dateLabels = {!! json_encode($dateLabels) !!};
            const chartViews = {!! json_encode($chartViews) !!};
            const chartVisitors = {!! json_encode($chartVisitors) !!};
            const chartDownloads = {!! json_encode($chartDownloads) !!};

            // 1. Traffic Chart (Line & Area)
            const ctxTraffic = document.getElementById('trafficChart');
            if (ctxTraffic) {
                new Chart(ctxTraffic, {
                    type: 'line',
                    data: {
                        labels: dateLabels,
                        datasets: [{
                                label: 'Page Views',
                                data: chartViews,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#2563eb',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Unique Visitors',
                                data: chartVisitors,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        weight: 600
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: {
                                    size: 13,
                                    weight: 700
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Downloads Chart (Bar)
            const ctxDownloads = document.getElementById('downloadsChart');
            if (ctxDownloads) {
                new Chart(ctxDownloads, {
                    type: 'bar',
                    data: {
                        labels: dateLabels,
                        datasets: [{
                            label: 'Downloads',
                            data: chartDownloads,
                            backgroundColor: '#7c3aed',
                            borderRadius: 6,
                            hoverBackgroundColor: '#6d28d9',
                            maxBarThickness: 24,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 3. Types Chart (Doughnut)
            const ctxTypes = document.getElementById('typesChart');
            if (ctxTypes) {
                new Chart(ctxTypes, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($typeLabels) !!},
                        datasets: [{
                            data: {!! json_encode($typeCounts) !!},
                            backgroundColor: [
                                '#2563eb', // Web (Blue)
                                '#10b981', // Excel (Green)
                                '#0284c7', // Word (Sky)
                                '#7c3aed', // Design (Purple)
                                '#f59e0b', // UI Kit (Amber)
                                '#ec4899', // Presentation (Pink)
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    font: {
                                        size: 11,
                                        weight: 600
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 4. Devices Chart (Polar Area / Doughnut)
            const ctxDevices = document.getElementById('devicesChart');
            if (ctxDevices) {
                const deviceData = {!! json_encode($devices) !!};
                new Chart(ctxDevices, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(deviceData),
                        datasets: [{
                            data: Object.values(deviceData),
                            backgroundColor: ['#2563eb', '#38bdf8', '#a855f7'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    font: {
                                        size: 11,
                                        weight: 600
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 5. Browsers Chart (Bar / Doughnut)
            const ctxBrowsers = document.getElementById('browsersChart');
            if (ctxBrowsers) {
                const browserData = {!! json_encode($browsers) !!};
                new Chart(ctxBrowsers, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(browserData),
                        datasets: [{
                            label: 'Sessions',
                            data: Object.values(browserData),
                            backgroundColor: [
                                '#3b82f6',
                                '#06b6d4',
                                '#f97316',
                                '#8b5cf6',
                                '#64748b'
                            ],
                            borderRadius: 5,
                            maxBarThickness: 18,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
