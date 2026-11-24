<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\ApiResponse;
use App\Models\UserSession;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return ApiResponse::error('Unauthorized', null, 401);
        }

        $token = substr($authHeader, 7);

        try {
            $secret  = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            $payload = (array) $decoded;

            if (($payload['type'] ?? null) !== 'access') {
                return ApiResponse::error('Unauthorized', null, 401);
            }

            $sessionId = $payload['session_id'] ?? null;
            $userId    = $payload['users_id'] ?? $payload['sub'] ?? null;

            if (!$sessionId || !$userId) {
                return ApiResponse::error('Unauthorized', null, 401);
            }

            $session = Session::where('id', $sessionId)
                ->where('access_token', $token)
                ->first();

            if (!$session) {
                return ApiResponse::error('Unauthorized', null, 401);
            }

            $user = User::where('id', $userId)->first();

            if (!$user) {
                return ApiResponse::error('Unauthorized', null, 401);
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $request->attributes->set('user_id', $user->id);
            $request->attributes->set('session_id', $sessionId);

            return $next($request);
        } catch (\Throwable $th) {
            return ApiResponse::error('Unauthorized', $th->getMessage(), 401);
        }
    }
}
