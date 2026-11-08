<?php

namespace App\Services;

use App\Exceptions\BusinessConflict;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KategoriService
{
    public function daftar(): Collection
    {
        return Kategori::query()->orderBy('nama')->get();
    }

    public function simpan(array $data, ?Kategori $kategori = null): Kategori
    {
        $kategori ??= new Kategori;
        $kategori->fill($data)->save();

        return $kategori;
    }

    public function hapus(Kategori $kategori): void
    {
        DB::transaction(function () use ($kategori): void {
            $locked = Kategori::query()->lockForUpdate()->findOrFail($kategori->id);
            if ($locked->buku()->withTrashed()->exists()) {
                throw new BusinessConflict('Kategori masih digunakan buku, termasuk buku yang diarsipkan.');
            }
            $locked->delete();
        });
    }
}
