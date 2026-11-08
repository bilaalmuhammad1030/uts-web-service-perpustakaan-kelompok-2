<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatStokResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'buku_id' => $this->buku_id,
            'petugas' => ['id' => $this->petugas->id, 'nama' => $this->petugas->nama],
            'total_sebelum' => $this->total_sebelum, 'tersedia_sebelum' => $this->tersedia_sebelum,
            'total_sesudah' => $this->total_sesudah, 'tersedia_sesudah' => $this->tersedia_sesudah,
            'versi_stok' => $this->versi_stok, 'alasan' => $this->alasan,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
