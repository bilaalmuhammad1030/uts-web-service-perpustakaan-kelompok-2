<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'kode_buku' => $this->kode_buku, 'judul' => $this->judul,
            'penulis' => $this->penulis, 'deskripsi' => $this->deskripsi, 'gambar_url' => $this->gambar_url, 'isbn' => $this->isbn,
            'penerbit' => $this->penerbit, 'tahun_terbit' => $this->tahun_terbit,
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'lokasi_rak' => $this->lokasi_rak, 'jumlah_total' => $this->jumlah_total,
            'jumlah_tersedia' => $this->jumlah_tersedia, 'versi_stok' => $this->versi_stok,
            'stok_diperbarui_at' => $this->stok_diperbarui_at?->toISOString(),
        ];
    }
}
