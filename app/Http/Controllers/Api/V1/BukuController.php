<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BukuRequest;
use App\Http\Requests\DaftarBukuRequest;
use App\Http\Requests\StokRequest;
use App\Http\Resources\BukuResource;
use App\Http\Resources\RiwayatStokResource;
use App\Models\Buku;
use App\Services\BukuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BukuController extends Controller
{
    public function __construct(private readonly BukuService $service) {}

    public function index(DaftarBukuRequest $request): AnonymousResourceCollection
    {
        return BukuResource::collection($this->service->daftar($request->validated()));
    }

    public function show(Buku $buku): BukuResource
    {
        return new BukuResource($buku->load('kategori'));
    }

    public function store(BukuRequest $request): JsonResponse
    {
        $buku = $this->service->buat($request->validated(), $request->user());

        return (new BukuResource($buku))->response()->setStatusCode(201)->header('Location', route('api.v1.buku.show', $buku));
    }

    public function update(BukuRequest $request, Buku $buku): BukuResource
    {
        return new BukuResource($this->service->ubah($buku, $request->validated()));
    }

    public function stok(StokRequest $request, Buku $buku): BukuResource
    {
        return new BukuResource($this->service->stok($buku, $request->validated(), $request->user()));
    }

    public function riwayat(Buku $buku): AnonymousResourceCollection
    {
        return RiwayatStokResource::collection($buku->riwayatStok()->with('admin')->latest('id')->paginate(20));
    }

    public function destroy(Buku $buku): Response
    {
        $this->service->hapus($buku);

        return response()->noContent();
    }
}
