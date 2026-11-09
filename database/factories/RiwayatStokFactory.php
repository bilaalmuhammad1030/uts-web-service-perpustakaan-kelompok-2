<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiwayatStokFactory extends Factory
{
    public function definition(): array
    {
        return ['buku_id' => Buku::factory(), 'admin_id' => Admin::factory(),
            'total_sebelum' => 0, 'tersedia_sebelum' => 0, 'total_sesudah' => 10, 'tersedia_sesudah' => 7,
            'versi_stok' => 1, 'alasan' => 'Data uji awal'];
    }
}
