<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'EzyTemplate Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        {!! file_get_contents(resource_path('views/admin/layouts/theme.css')) !!}
    </style>
</head>

<body>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand"
                style="padding: 18px 20px; display: flex; align-items: center; justify-content: flex-start;">
                <img src="/assets/logo-white.png" alt="EzyTemplate"
                    style="height: 36px; width: auto; object-fit: contain;">
            </div>
            <div class="side-scroll">
                <a class="navitem {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}"><b>⌂</b> Dashboard</a>
                <div class="navgroup open"><button class="groupbtn" data-toggle="analytics"><span>◈</span> Analytics
                        <i>⌄</i></button>
                    <div class="submenu" id="analytics"><a
                            class="{{ request()->routeIs('admin.analytics.visitors') ? 'active' : '' }}"
                            href="{{ route('admin.analytics.visitors') }}">Visitors & Traffic</a><a
                            class="{{ request()->routeIs('admin.analytics.resources') ? 'active' : '' }}"
                            href="{{ route('admin.analytics.resources') }}">Resource Views</a><a
                            class="{{ request()->routeIs('admin.analytics.files') ? 'active' : '' }}"
                            href="{{ route('admin.analytics.files') }}">File Analytics</a><a
                            class="{{ request()->routeIs('admin.analytics.downloads') ? 'active' : '' }}"
                            href="{{ route('admin.analytics.downloads') }}">Downloads</a></div>
                </div>
                <div class="navgroup open"><button class="groupbtn" data-toggle="cms"><span>✦</span> Website CMS
                        <i>⌄</i></button>
                    <div class="submenu" id="cms"><a
                            class="{{ request()->routeIs('admin.cms.home') ? 'active' : '' }}"
                            href="{{ route('admin.cms.home') }}">Homepage CMS</a><a
                            class="{{ request()->routeIs('admin.cms.about') ? 'active' : '' }}"
                            href="{{ route('admin.cms.about') }}">About & Team</a><a
                            class="{{ request()->routeIs('admin.cms.services') ? 'active' : '' }}"
                            href="{{ route('admin.cms.services') }}">Services & FAQs</a><a
                            class="{{ request()->routeIs('admin.cms.custom-pages') ? 'active' : '' }}"
                            href="{{ route('admin.cms.custom-pages') }}">System Pages</a><a
                            class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                            href="{{ route('admin.settings') }}">Branding & Menus</a></div>
                </div>
                <div class="navgroup open"><button class="groupbtn" data-toggle="catalog"><span>▦</span> Catalog
                        <i>⌄</i></button>
                    <div class="submenu" id="catalog"><a href="{{ route('admin.resources.index') }}">Resources</a><a
                            href="{{ route('admin.resources.create') }}">Add Resource</a><a
                            href="{{ route('admin.categories.index') }}">Categories</a></div>
                </div>
                <div class="navgroup"><button class="groupbtn" data-toggle="content"><span>✍</span> Content
                        <i>⌄</i></button>
                    <div class="submenu" id="content"><a href="{{ route('admin.articles.index') }}">Articles</a><a
                            href="{{ route('admin.newsletter.index') }}">Newsletter</a><a
                            href="{{ route('admin.requests.index') }}">Service Requests</a></div>
                </div>
                <div class="navgroup open"><button class="groupbtn" data-toggle="users"><span>♙</span> Users & Security
                        <i>⌄</i></button>
                    <div class="submenu" id="users">
                        <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">User Accounts</a>
                        <a class="{{ request()->routeIs('admin.login-logs.*') ? 'active' : '' }}"
                            href="{{ route('admin.login-logs.index') }}">Login Activities</a>
                    </div>
                </div>
            </div>
            <div class="side-bottom">
                <div class="admin-mini"><span
                        class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div><b>{{ auth()->user()->name }}</b><small>{{ auth()->user()->role }}</small></div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout">↪ Sign
                        out</button></form>
            </div>
        </aside>
        <main class="main">
            <header class="topbar"><button class="mobile-toggle" id="mobileToggle">☰</button>
                <div><span class="crumb">ADMIN / @yield('section', 'OVERVIEW')</span>
                    <h1>@yield('heading', 'Dashboard')</h1>
                </div>
                <div class="top-actions"><a href="/" target="_blank" class="ghost">View site ↗</a><span
                        class="live"><i></i> Live</span></div>
            </header>
            <div class="content">
                @if (session('success'))
                    <div class="flash success">✓ {{ session('success') }}</div>
                    @endif @if (isset($errors) && $errors->any())
                        <div class="flash error">{{ $errors->first() }}</div>
                    @endif @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        {!! file_get_contents(resource_path('views/admin/layouts/theme.js')) !!}
    </script>
</body>

</html>
