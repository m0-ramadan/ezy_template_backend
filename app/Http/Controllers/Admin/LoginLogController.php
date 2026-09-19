<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->latest();

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by Email, IP, or Reason
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('email', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%")
                    ->orWhere('browser', 'like', "%{$q}%")
                    ->orWhere('device', 'like', "%{$q}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Statistics KPI Cards
        $totalLogins = LoginLog::count();
        $successfulLogins = LoginLog::where('status', 'success')->count();
        $failedLogins = LoginLog::where('status', 'failed')->count();
        $newRegistrations = LoginLog::where('status', 'registered')->count();
        $uniqueUsersToday = LoginLog::whereDate('created_at', today())->distinct('email')->count('email');

        return view('admin.users.login_logs', compact(
            'logs',
            'totalLogins',
            'successfulLogins',
            'failedLogins',
            'newRegistrations',
            'uniqueUsersToday'
        ));
    }

    public function destroy(LoginLog $loginLog)
    {
        $loginLog->delete();
        return back()->with('success', 'Log entry removed.');
    }

    public function clear()
    {
        LoginLog::truncate();
        return back()->with('success', 'All login history has been cleared.');
    }
}
