<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $user = auth()->user();

            if (!$user || !in_array($user->role_id, $roles)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }

            return $next($request);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
