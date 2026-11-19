<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (! is_string($authorization) || ! str_starts_with($authorization, 'Bearer ')) {
            return response()->json([
                'message' => 'Autentikasi diperlukan.',
            ], 401);
        }

        $token = substr($authorization, 7);
        $user = User::where('remember_token', $token)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Token tidak valid atau sudah logout.',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
