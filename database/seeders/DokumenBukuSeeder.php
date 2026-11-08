<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Petugas;
use App\Services\BukuService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumenBukuSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(database_path('seeders/data/buku-dokumen.json')), true, flags: JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($rows): void {
            $petugas = Petugas::where('email', 'petugas@perpustakaan.test')->firstOrFail();
            foreach ($rows as $row) {
                if (Buku::withTrashed()->where('kode_buku', $row['kode_buku'])->exists()) {
                    continue;
                }
                $kategori = Kategori::firstOrCreate(['nama' => $row['kategori']]);
                unset($row['kategori']);
                app(BukuService::class)->buat([...$row, 'kategori_id' => $kategori->id,
                    'deskripsi' => 'Data contoh sesuai dokumen Data dan Pengelolaan Buku Perpustakaan.'], $petugas);
            }
        });
    }
}
