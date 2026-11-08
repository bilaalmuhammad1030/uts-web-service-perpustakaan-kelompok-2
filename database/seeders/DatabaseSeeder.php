<?php

namespace Database\Seeders;

use App\Models\Petugas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('services.demo.password');
        if (! is_string($password) || strlen($password) < 12) {
            throw new \RuntimeException('Isi DEMO_PASSWORD minimal 12 karakter pada .env sebelum menjalankan seeder.');
        }
        DB::transaction(function () use ($password): void {
            Petugas::firstOrCreate(['email' => 'petugas@perpustakaan.test'], ['nama' => 'Petugas Kelompok 2', 'password' => $password]);
            $this->call(DokumenBukuSeeder::class);
        });
    }
}
