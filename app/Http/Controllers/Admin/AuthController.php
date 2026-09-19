<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email = $data['email'];

        if (Auth::attempt(['email' => $email, 'password' => $data['password'], 'status' => 'active'], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = auth()->user();

            if (!in_array($user->role, ['admin', 'editor'], true)) {
                LoginLog::log($request, $email, 'failed', $user, 'Non-admin role access rejected');
                Auth::logout();
                return back()->withErrors(['email' => 'Admin access required.']);
            }

            LoginLog::log($request, $email, 'success', $user, 'Admin dashboard login');
            return redirect()->intended(route('admin.dashboard'));
        }

        LoginLog::log($request, $email, 'failed', null, 'Invalid credentials');
        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
