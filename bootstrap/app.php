<?php

use App\Exceptions\BusinessConflict;
use App\Http\Middleware\AuthenticatePetugas;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);
        $middleware->alias(['petugas' => AuthenticatePetugas::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (Throwable $e, Request $request): ?JsonResponse {
            if (! $request->is('api/*')) {
                return null;
            }
            $status = 500;
            $code = 'INTERNAL_ERROR';
            $message = 'Terjadi gangguan pada server.';
            $details = (object) [];
            if ($e instanceof ValidationException) {
                $status = 422;
                $code = 'VALIDATION_ERROR';
                $message = 'Periksa kembali data yang dikirim.';
                $details = $e->errors();
            } elseif ($e instanceof BusinessConflict) {
                $status = 409;
                $code = 'BUSINESS_CONFLICT';
                $message = $e->getMessage();
            } elseif ($e instanceof AuthenticationException) {
                $status = 401;
                $code = 'UNAUTHENTICATED';
                $message = $e->getMessage();
            } elseif ($e instanceof QueryException && in_array((string) $e->getCode(), ['23000', '23505'])) {
                $status = 409;
                $code = 'BUSINESS_CONFLICT';
                $message = 'Data bertentangan dengan data yang sudah tersimpan. Muat ulang dan periksa relasinya.';
            } elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $code = match ($status) {
                    404 => 'NOT_FOUND', 405 => 'METHOD_NOT_ALLOWED', 429 => 'TOO_MANY_REQUESTS', default => 'HTTP_ERROR'
                };
                $message = match ($status) {
                    404 => 'Data atau endpoint tidak ditemukan.', 405 => 'Metode HTTP tidak diizinkan.', 429 => 'Terlalu banyak permintaan. Coba lagi sebentar.', default => 'Permintaan tidak dapat diproses.'
                };
            }
            $headers = $e instanceof HttpExceptionInterface ? $e->getHeaders() : [];

            return response()->json(['error' => compact('code', 'message', 'details')], $status, $headers);
        });
    })->create();
