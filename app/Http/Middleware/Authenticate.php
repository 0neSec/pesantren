<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    
    protected function redirectTo($request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Anda tidak memiliki akses'
        ], 401);
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'status' => 'error',
            'message' => 'Anda tidak memiliki akses'
        ], 401));
    }
}
