<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthService $service): JsonResponse
    {
        return response()->json(['data' => $service->login($request->validated())])->header('Cache-Control', 'no-store');
    }

    public function logout(Request $request, AuthService $service): Response
    {
        $service->logout($request->user());

        return response()->noContent();
    }
}
