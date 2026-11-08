<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_buku' => fake()->unique()->bothify('BK-####??'), 'judul' => fake()->sentence(4),
            'penulis' => fake()->name(), 'kategori_id' => Kategori::factory(), 'lokasi_rak' => 'A-01',
            'penerbit' => 'Penerbit Contoh', 'tahun_terbit' => 2020,
            'deskripsi' => fake()->paragraph(), 'isbn' => null, 'jumlah_total' => 10, 'jumlah_tersedia' => 7,
            'versi_stok' => 1, 'stok_diperbarui_at' => now(),
        ];
    }
}
