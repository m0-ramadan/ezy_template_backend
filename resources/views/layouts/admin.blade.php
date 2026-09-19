<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'EzyTemplate Admin')</title>
    <style>
        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, sans-serif;
            background: #f6f8fc;
            color: #14213d
        }

        .shell {
            display: flex;
            min-height: 100vh
        }

        .side {
            width: 255px;
            background: #0f172a;
            color: #dbe6f7;
            padding: 22px 15px;
            position: fixed;
            inset: 0 auto 0 0
        }

        .brand {
            font-size: 21px;
            font-weight: 800;
            padding: 9px 12px 25px
        }

        .brand span {
            color: #4f8cff
        }

        .nav a {
            display: flex;
            gap: 11px;
            padding: 12px 13px;
            border-radius: 10px;
            color: #aebbd0;
            text-decoration: none;
            margin: 4px 0;
            font-size: 14px
        }

        .nav a:hover,
        .nav a.active {
            background: #1e3a63;
            color: #fff
        }

        .main {
            margin-left: 255px;
            width: calc(100% - 255px)
        }

        .top {
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #e7edf5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 5
        }

        .top h1 {
            font-size: 20px;
            margin: 0
        }

        .user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: white;
            display: grid;
            place-items: center;
            font-weight: 700
        }

        .content {
            padding: 28px 30px
        }

        .grid {
            display: grid;
            gap: 18px
        }

        .stats {
            grid-template-columns: repeat(4, 1fr)
        }

        .stat {
            background: #fff;
            border: 1px solid #e6edf6;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 24px #14213d08
        }

        .stat small {
            color: #718096
        }

        .stat strong {
            display: block;
            font-size: 28px;
            margin: 7px 0
        }

        .card {
            background: #fff;
            border: 1px solid #e6edf6;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 24px #14213d06
        }

        .split {
            grid-template-columns: 1.5fr 1fr;
            margin-top: 20px
        }

        .table {
            width: 100%;
            border-collapse: collapse
        }

        .table th,
        .table td {
            text-align: left;
            padding: 13px 10px;
            border-bottom: 1px solid #edf1f6;
            font-size: 13px
        }

        .table th {
            color: #65748b;
            font-weight: 600
        }

        .btn {
            border: 0;
            background: #2563eb;
            color: #fff;
            padding: 10px 15px;
            border-radius: 9px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer
        }

        .btn.secondary {
            background: #eef4ff;
            color: #2563eb
        }

        .btn.danger {
            background: #feecec;
            color: #dc2626
        }

        .toolbar {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px
        }

        .input,
        .select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #dce4ef;
            border-radius: 9px;
            background: #fff;
            font: inherit
        }

        .filters {
            display: flex;
            gap: 10px;
            max-width: 620px
        }

        .formgrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px
        }

        .field.full {
            grid-column: 1/-1
        }

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            background: #e9f8ef;
            color: #16834a;
            margin-bottom: 15px
        }

        .error {
            background: #fff0f0;
            color: #b91c1c
        }

        .badge {
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: #e8f1ff;
            color: #2563eb
        }

        .badge.green {
            background: #ddf7ea;
            color: #138a59
        }

        .badge.gray {
            background: #eef2f7;
            color: #64748b
        }

        .login {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #f7fbff, #f4efff)
        }

        .loginbox {
            width: min(410px, calc(100% - 30px));
            background: white;
            border: 1px solid #e5ebf4;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 50px #14213d14
        }

        .loginbox h1 {
            margin: 0 0 6px
        }

        .loginbox p {
            color: #718096
        }

        .loginbox .field {
            margin: 15px 0
        }

        .check {
            display: flex;
            gap: 7px;
            font-size: 13px;
            margin: 12px 0
        }

        .fullbtn {
            width: 100%
        }

        @media(max-width:900px) {
            .side {
                width: 72px
            }

            .brand {
                font-size: 0
            }

            .brand span {
                font-size: 20px
            }

            .nav a span {
                display: none
            }

            .main {
                margin-left: 72px;
                width: calc(100% - 72px)
            }

            .stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .split {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:600px) {
            .content {
                padding: 18px
            }

            .top {
                padding: 0 18px
            }

            .stats {
                grid-template-columns: 1fr
            }

            .formgrid {
                grid-template-columns: 1fr
            }

            .field.full {
                grid-column: auto
            }

            .filters {
                flex-direction: column
            }
        }
    </style>
</head>

<body>
    <div class="shell">
        <aside class="side">
            <div class="brand">Ezy<span>Template</span></div>
            <nav class="nav">
                <a href="{{ route('admin.dashboard') }}">◈ <span>Dashboard</span></a><a
                    href="{{ route('admin.resources.index') }}">▣ <span>Resources</span></a><a
                    href="{{ route('admin.categories.index') }}">▦ <span>Categories</span></a><a
                    href="{{ route('admin.tools.index') }}">🛠 <span>Tools</span></a><a
                    href="{{ route('admin.articles.index') }}">◫ <span>Articles</span></a><a
                    href="{{ route('admin.users.index') }}">♙ <span>Users</span></a><a
                    href="{{ route('admin.requests.index') }}">✉ <span>Requests</span></a><a
                    href="{{ route('admin.newsletter.index') }}">☷ <span>Newsletter</span></a><a
                    href="{{ route('admin.settings') }}">⚙ <span>Settings</span></a>
            </nav>
        </aside>
        <main class="main">
            <header class="top">
                <h1>@yield('heading', 'Dashboard')</h1>
                <div class="user"><span>{{ auth()->user()->name }}</span>
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <form method="post" action="{{ route('admin.logout') }}">@csrf<button
                            class="btn secondary">Logout</button></form>
                </div>
            </header>
            <section class="content">
                @if (session('success'))
                    <div class="alert">{{ session('success') }}</div>
                    @endif @if ($errors->any())
                        <div class="alert error">{{ $errors->first() }}</div>
                    @endif @yield('content')
            </section>
        </main>
    </div>
</body>

</html>
