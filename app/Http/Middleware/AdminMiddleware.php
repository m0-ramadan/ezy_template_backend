<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class AdminMiddleware { public function handle(Request $request, Closure $next): Response { if(!auth()->check() || !in_array(auth()->user()->role,['admin','editor'],true) || auth()->user()->status!=='active') return $request->expectsJson()?response()->json(['message'=>'Unauthenticated'],401):redirect()->route('admin.login'); return $next($request); } }
