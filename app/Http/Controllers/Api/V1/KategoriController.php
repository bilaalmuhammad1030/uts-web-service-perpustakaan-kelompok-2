<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use App\Services\KategoriService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class KategoriController extends Controller
{
    public function __construct(private readonly KategoriService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return KategoriResource::collection($this->service->daftar());
    }

    public function show(Kategori $kategori): KategoriResource
    {
        return new KategoriResource($kategori);
    }

    public function store(KategoriRequest $request): JsonResponse
    {
        $kategori = $this->service->simpan($request->validated());

        return (new KategoriResource($kategori))->response()->setStatusCode(201)->header('Location', route('api.v1.kategori.show', $kategori));
    }

    public function update(KategoriRequest $request, Kategori $kategori): KategoriResource
    {
        return new KategoriResource($this->service->simpan($request->validated(), $kategori));
    }

    public function destroy(Kategori $kategori): Response
    {
        $this->service->hapus($kategori);

        return response()->noContent();
    }
}
