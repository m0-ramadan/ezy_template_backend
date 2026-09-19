<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * User Login API
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            LoginLog::log($request, $request->input('email', 'unknown'), 'failed', null, 'Validation error');
            return response()->json([
                'ok'      => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            LoginLog::log($request, $email, 'failed', null, 'User does not exist');
            return response()->json([
                'ok'      => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($user->status === 'blocked') {
            LoginLog::log($request, $email, 'failed', $user, 'Account is blocked');
            return response()->json([
                'ok'      => false,
                'message' => 'Your account has been suspended. Please contact support.',
            ], 403);
        }

        if (!Hash::check($password, $user->password)) {
            LoginLog::log($request, $email, 'failed', $user, 'Incorrect password');
            return response()->json([
                'ok'      => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Successful Login
        LoginLog::log($request, $email, 'success', $user);

        // Simple token generation (base64 random token)
        $token = base64_encode($user->id . '|' . md5($user->email . time() . 'ezy_secret'));

        return response()->json([
            'ok'      => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * User Registration API
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            LoginLog::log($request, $request->input('email', 'unknown'), 'failed', null, $validator->errors()->first());
            return response()->json([
                'ok'      => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));

        $user = User::create([
            'name'     => trim($request->input('name')),
            'email'    => $email,
            'password' => Hash::make($request->input('password')),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        LoginLog::log($request, $email, 'registered', $user, 'New account created');

        $token = base64_encode($user->id . '|' . md5($user->email . time() . 'ezy_secret'));

        return response()->json([
            'ok'      => true,
            'message' => 'Account created successfully.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 201);
    }
}
