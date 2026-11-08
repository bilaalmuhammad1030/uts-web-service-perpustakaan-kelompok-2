<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\BookMetadataProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\MetadataRequest;
use Illuminate\Http\JsonResponse;

class MetadataController extends Controller
{
    public function __invoke(MetadataRequest $request, BookMetadataProvider $provider): JsonResponse
    {
        return response()->json(['data' => $provider->cari($request->validated('q'))]);
    }
}
