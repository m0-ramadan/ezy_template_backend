@extends('layouts.admin')

@section('title', 'Tools & Capabilities Management')
@section('heading', 'Tools & Capabilities Management')

@section('content')
    <div class="grid">
        <!-- Server Capability Status Card -->
        <div class="card">
            <h3>Server Capabilities & Dependencies Status</h3>
            <p style="color: #64748b; font-size: 13px;">Overview of binary tools and PHP extensions required for heavy
                conversions.</p>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-top: 16px;">
                @foreach ($capabilities as $key => $available)
                    <div
                        style="padding: 12px 14px; border-radius: 10px; background: {{ $available ? '#f0fdf4' : '#fef2f2' }}; border: 1px solid {{ $available ? '#bbf7d0' : '#fecaca' }};">
                        <strong
                            style="text-transform: uppercase; font-size: 11px; color: {{ $available ? '#166534' : '#991b1b' }}; display: block;">
                            {{ $key }}
                        </strong>
                        <span style="font-weight: 700; font-size: 14px; color: {{ $available ? '#15803d' : '#dc2626' }};">
                            {{ $available ? '✓ Available' : '✗ Unavailable' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tools Analytics Summary -->
        <div class="stats grid">
            <div class="stat">
                <small>Total Events Tracked</small>
                <strong>{{ number_format($analytics['total_events']) }}</strong>
            </div>
            <div class="stat">
                <small>Tool Usage Count</small>
                <strong>{{ number_format($analytics['total_tool_uses']) }}</strong>
            </div>
            <div class="stat">
                <small>Successful Operations</small>
                <strong style="color: #16a34a;">{{ number_format($analytics['successful']) }}</strong>
            </div>
            <div class="stat">
                <small>Failed Operations</small>
                <strong style="color: #dc2626;">{{ number_format($analytics['failed']) }}</strong>
            </div>
        </div>

        <!-- Active Tools Table -->
        <div class="card">
            <h3>Managed Tools List ({{ count($tools) }})</h3>
            <table class="table" style="margin-top: 14px;">
                <thead>
                    <tr>
                        <th>Tool Name</th>
                        <th>Category</th>
                        <th>Class</th>
                        <th>Dependency</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tools as $tool)
                        <tr>
                            <td>
                                <strong>{{ $tool->name }}</strong><br>
                                <small style="color:#64748b;">{{ $tool->slug }}</small>
                            </td>
                            <td><span class="badge gray">{{ $tool->category->name ?? 'Uncategorized' }}</span></td>
                            <td><code>{{ $tool->tool_class }}</code></td>
                            <td>
                                @if ($tool->required_dependency)
                                    @php
                                        $isAvail = \App\Services\ServerCapabilityService::isDependencyAvailable(
                                            $tool->required_dependency,
                                        );
                                    @endphp
                                    <span class="badge {{ $isAvail ? 'green' : 'danger' }}">
                                        {{ $tool->required_dependency }} ({{ $isAvail ? 'OK' : 'Missing' }})
                                    </span>
                                @else
                                    <span style="color:#94a3b8;">None (Client)</span>
                                @endif
                            </td>
                            <td><strong>{{ number_format($tool->usage_count) }}</strong></td>
                            <td>
                                <span class="badge {{ $tool->is_active ? 'green' : 'gray' }}">
                                    {{ $tool->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.tools.toggle', $tool) }}"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $tool->is_active ? 'danger' : 'secondary' }}"
                                        style="padding: 5px 10px; font-size: 11px;">
                                        {{ $tool->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
