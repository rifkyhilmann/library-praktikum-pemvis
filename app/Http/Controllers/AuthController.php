<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use App\Support\ApiResponse;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                return ApiResponse::error('Invalid Credentials', null, 404);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::error('Invalid Credentials', null, 404);
            }

            $session = Session::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'access_token' => '',
                'refresh_token' => '',
            ]);

            $sessionId = $session->id;
            $now = time();

            $accessTtl  = (int) env('JWT_ACCESS_TTL', 604800);
            $refreshTtl = (int) env('JWT_REFRESH_TTL', 2592000);

            $accessPayload = [
                'iss'         => config('app.url'),
                'iat'         => $now,
                'exp'         => $now + $accessTtl,
                'sub'         => $user->id,
                'session_id'  => $sessionId,
                'users_id'    => $user->id,
                'jti'         => (string) Str::uuid(),
                'role'        => $user->role,
                'type'        => 'access',
            ];

            $refreshPayload = [
                'iss'         => config('app.url'),
                'iat'         => $now,
                'exp'         => $now + $refreshTtl,
                'sub'         => $user->id,
                'session_id'  => $sessionId,
                'users_id'    => $user->id,
                'jti'         => (string) Str::uuid(),
                'type'        => 'refresh',
            ];

            $secret = env('JWT_SECRET');

            $accessToken  = JWT::encode($accessPayload,  $secret, 'HS256');
            $refreshToken = JWT::encode($refreshPayload, $secret, 'HS256');

            $session->update([
                'access_token'  => $accessToken,
                'refresh_token' => $refreshToken,
                'updated_at'    => now(),
            ]);

            return ApiResponse::success('Login Success', [
                'access_token'  => $accessToken,
                'refresh_token' => $refreshToken,
                'users'         => $user
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e, 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $sessionId = $request->attributes->get('session_id');

            if (!$sessionId) {
                return ApiResponse::error('Session not found', null, 404);
            }

            $session = Session::where('id', $sessionId)->first();

            if (!$session) {
                return ApiResponse::error('Session not found', null, 404);
            }

            $session->delete();

            return ApiResponse::success('Logout Success', null);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e, 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
            ]);

            return ApiResponse::success('Register Success', $user, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::error('Unauthorized', null, 401);
            }

            return ApiResponse::success('Profile retrieved successfully', $user);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }
}
