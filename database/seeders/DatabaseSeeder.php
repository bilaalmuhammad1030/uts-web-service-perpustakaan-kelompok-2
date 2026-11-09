<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('services.demo.password') ?: 'Admin';
        DB::transaction(function () use ($password): void {
            Admin::firstOrCreate(
                ['email' => 'Admin'],
                ['nama' => 'Admin', 'password' => $password]
            );
            $this->call(DokumenBukuSeeder::class);
        });
    }
}
