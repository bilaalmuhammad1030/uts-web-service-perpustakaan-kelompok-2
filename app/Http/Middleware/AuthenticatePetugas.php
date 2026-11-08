<?php

namespace App\Http\Middleware;

use App\Models\Petugas;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePetugas
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $petugas = $token ? Petugas::query()->where('token_hash', hash('sha256', $token))->where('token_expires_at', '>', now())->first() : null;
        if (! $petugas) {
            throw new AuthenticationException('Silakan login sebagai petugas.');
        }
        $request->setUserResolver(fn () => $petugas);

        return $next($request);
    }
}
