<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{VisitorSession, PageView, ResourceView};

class TrackVisitor
{
  public function handle(Request $request, Closure $next)
  {
    if ($request->is('admin/*') || $request->is('admin') || $request->is('api/stats/realtime') || $request->is('api/*')) return $next($request);
    $start = microtime(true);
    $ip = $request->ip() ?? '';
    $ua = (string)$request->userAgent();
    $hash = hash('sha256', $ip . '|' . $ua . '|' . config('app.key'));
    $sid = $request->hasSession() ? $request->session()->getId() : null;
    $vs = VisitorSession::firstOrCreate(['visitor_hash' => $hash], ['session_id' => $sid, 'ip_address' => $ip, 'user_agent' => $ua, 'first_seen' => now(), 'last_seen' => now()]);
    $vs->update(['last_seen' => now(), 'session_id' => $sid, 'ip_address' => $ip, 'user_agent' => $ua, 'page_views' => DB::raw('page_views + 1')]);
    $response = $next($request);
    $ms = (int)round((microtime(true) - $start) * 1000);
    PageView::create(['visitor_hash' => $hash, 'session_id' => $sid, 'user_id' => optional($request->user())->id, 'path' => '/' . $request->path(), 'route_name' => optional($request->route())->getName(), 'method' => $request->method(), 'ip_address' => $ip, 'user_agent' => $ua, 'referer' => $request->headers->get('referer'), 'response_ms' => min($ms, 65535), 'created_at' => now()]);
    if ($request->route('resource')) {
      $resource = $request->route('resource');
      if (is_object($resource) && isset($resource->id)) {
        ResourceView::create(['resource_id' => $resource->id, 'visitor_hash' => $hash, 'session_id' => $sid, 'user_id' => optional($request->user())->id, 'ip_address' => $ip, 'user_agent' => $ua, 'referer' => $request->headers->get('referer'), 'created_at' => now()]);
      }
    }
    return $response;
  }
}
