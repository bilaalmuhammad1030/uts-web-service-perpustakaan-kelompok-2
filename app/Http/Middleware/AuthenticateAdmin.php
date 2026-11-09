<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $admin = $token ? Admin::query()->where('token_hash', hash('sha256', $token))->where('token_expires_at', '>', now())->first() : null;
        if (! $admin) {
            throw new AuthenticationException('Silakan login sebagai admin.');
        }
        $request->setUserResolver(fn () => $admin);

        return $next($request);
    }
}
