@extends('admin.layouts.app')

@section('title', 'Login Activities & Security Logs — EzyTemplate Admin')
@section('section', 'USERS & SECURITY')
@section('heading', 'Authentication & Login History')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- KPI Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div class="panel"
                style="padding: 18px 20px; display: flex; align-items: center; gap: 14px; border-radius: 14px;">
                <div
                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(37,99,235,0.1); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    🔑
                </div>
                <div>
                    <small
                        style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total
                        Events</small>
                    <div style="font-size: 24px; font-weight: 800; color: var(--text);">{{ number_format($totalLogins) }}
                    </div>
                </div>
            </div>

            <div class="panel"
                style="padding: 18px 20px; display: flex; align-items: center; gap: 14px; border-radius: 14px;">
                <div
                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    ✓
                </div>
                <div>
                    <small
                        style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Successful
                        Logins</small>
                    <div style="font-size: 24px; font-weight: 800; color: #10b981;">{{ number_format($successfulLogins) }}
                    </div>
                </div>
            </div>

            <div class="panel"
                style="padding: 18px 20px; display: flex; align-items: center; gap: 14px; border-radius: 14px;">
                <div
                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    ✕
                </div>
                <div>
                    <small
                        style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Failed
                        Attempts</small>
                    <div style="font-size: 24px; font-weight: 800; color: #ef4444;">{{ number_format($failedLogins) }}</div>
                </div>
            </div>

            <div class="panel"
                style="padding: 18px 20px; display: flex; align-items: center; gap: 14px; border-radius: 14px;">
                <div
                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(139,92,246,0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    👤
                </div>
                <div>
                    <small
                        style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">New
                        Registrations</small>
                    <div style="font-size: 24px; font-weight: 800; color: #8b5cf6;">{{ number_format($newRegistrations) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Table Panel -->
        <section class="panel" style="border-radius: 16px;">
            <div class="panel-head"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 16px; margin: 0 0 4px;">Authentication Security Audit Logs</h3>
                    <span style="font-size: 12px; color: var(--muted);">Real-time monitoring of user logins, registrations,
                        IP addresses, and client devices</span>
                </div>
                <form method="POST" action="{{ route('admin.login-logs.clear') }}"
                    onsubmit="return confirm('Are you sure you want to permanently clear all login history logs?');">
                    @csrf
                    <button type="submit" class="btn outline"
                        style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 12px; padding: 8px 14px; border-radius: 8px;">
                        🗑️ Clear All Logs
                    </button>
                </form>
            </div>

            <!-- Filter Bar -->
            <div style="padding: 14px 18px; border-bottom: 1px solid var(--line); background: #f8fafc;">
                <form method="GET" action="{{ route('admin.login-logs.index') }}"
                    style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search by email, IP address, device, or browser..." class="input"
                        style="flex: 1; min-width: 260px; height: 40px; border-radius: 8px; background: #fff;">

                    <select name="status" class="select"
                        style="height: 40px; border-radius: 8px; min-width: 160px; background: #fff;">
                        <option value="all" @selected(!request('status') || request('status') === 'all')>All Statuses</option>
                        <option value="success" @selected(request('status') === 'success')>Success</option>
                        <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                        <option value="registered" @selected(request('status') === 'registered')>New Registration</option>
                    </select>

                    <button type="submit" class="btn primary" style="height: 40px; padding: 0 18px; border-radius: 8px;">
                        Filter Logs
                    </button>

                    @if (request('q') || (request('status') && request('status') !== 'all'))
                        <a href="{{ route('admin.login-logs.index') }}" class="btn outline"
                            style="height: 40px; padding: 0 16px; border-radius: 8px; display: inline-flex; align-items: center;">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Logs Table -->
            <div class="responsive-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User Account</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>Device & Browser</th>
                            <th>Details / Reason</th>
                            <th>Timestamp</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    @if ($log->user)
                                        <b>{{ $log->user->name }}</b>
                                        <span
                                            style="font-size: 10px; background: rgba(37,99,235,0.1); color: #2563eb; padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">{{ $log->user->role }}</span>
                                        <br>
                                    @endif
                                    <span
                                        style="font-family: monospace; font-size: 12px; color: var(--text);">{{ $log->email }}</span>
                                </td>
                                <td>
                                    @if ($log->status === 'success')
                                        <span
                                            style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                            ● Success
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span
                                            style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">
                                            ✕ Failed
                                        </span>
                                    @elseif($log->status === 'registered')
                                        <span
                                            style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #f5f3ff; color: #5b21b6; border: 1px solid #ddd6fe;">
                                            ★ Registered
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        style="font-family: monospace; font-size: 11px; padding: 2px 8px; background: var(--bg); border-radius: 6px; border: 1px solid var(--line);">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px;">
                                        <span>
                                            @if ($log->device === 'Mobile')
                                                📱
                                            @elseif($log->device === 'Tablet')
                                                📲
                                            @else
                                                💻
                                            @endif
                                        </span>
                                        <b>{{ $log->device ?? 'Desktop' }}</b>
                                        <span style="color: var(--muted);">•</span>
                                        <span style="color: var(--muted);">{{ $log->browser ?? 'Browser' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small style="color: var(--muted); font-size: 12px;">
                                        {{ $log->failure_reason ?? 'Standard auth' }}
                                    </small>
                                </td>
                                <td>
                                    <div style="font-size: 12px; font-weight: 600;">
                                        {{ $log->created_at->format('M d, Y H:i') }}
                                    </div>
                                    <small style="color: var(--muted); font-size: 11px;">
                                        {{ $log->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="{{ route('admin.login-logs.destroy', $log) }}"
                                        onsubmit="return confirm('Delete this log record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 14px; padding: 4px;"
                                            title="Delete log entry">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted);">
                                    No authentication log entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px; border-top: 1px solid var(--line);">
                {{ $logs->links() }}
            </div>
        </section>
    </div>
@endsection
