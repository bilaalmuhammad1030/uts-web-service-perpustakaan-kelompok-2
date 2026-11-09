<?php

namespace App\Services;

use App\Exceptions\BusinessConflict;
use App\Models\Admin;
use App\Models\Buku;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BukuService
{
    public function daftar(array $filter): LengthAwarePaginator
    {
        $query = Buku::query()->with('kategori');
        if ($q = $filter['q'] ?? null) {
            // Escape SQL LIKE wildcards: a search string is treated literally.
            $q = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q).'%';
            $query->where(fn ($builder) => $builder->where('judul', 'like', $q)->orWhere('penulis', 'like', $q));
        }
        if ($id = $filter['kategori_id'] ?? null) {
            $query->where('kategori_id', $id);
        }
        if (isset($filter['tersedia'])) {
            $query->where('jumlah_tersedia', $filter['tersedia'] ? '>' : '=', 0);
        }

        return $query->orderBy($filter['sort'] ?? 'judul', $filter['direction'] ?? 'asc')
            ->orderBy('id')->paginate($filter['per_page'] ?? 6)->withQueryString();
    }

    public function buat(array $data, Admin $admin): Buku
    {
        return DB::transaction(function () use ($data, $admin): Buku {
            $buku = Buku::create([...$data, 'versi_stok' => 1, 'stok_diperbarui_at' => now()]);
            $buku->riwayatStok()->create([
                'admin_id' => $admin->id, 'total_sebelum' => 0, 'tersedia_sebelum' => 0,
                'total_sesudah' => $buku->jumlah_total, 'tersedia_sesudah' => $buku->jumlah_tersedia,
                'versi_stok' => 1, 'alasan' => 'Pencatatan buku baru',
            ]);

            return $buku->load('kategori');
        }, 3);
    }

    public function ubah(Buku $buku, array $data): Buku
    {
        $buku->update($data);

        return $buku->refresh()->load('kategori');
    }

    public function stok(Buku $buku, array $data, Admin $admin): Buku
    {
        return DB::transaction(function () use ($buku, $data, $admin): Buku {
            // Read again INSIDE transaction, after acquiring the row lock.
            $locked = Buku::query()->lockForUpdate()->findOrFail($buku->id);
            if ($locked->versi_stok !== (int) $data['versi_stok']) {
                throw new BusinessConflict('Stok sudah diubah admin lain. Muat ulang detail buku dan kirim versi_stok terbaru.');
            }
            $before = [$locked->jumlah_total, $locked->jumlah_tersedia];
            $locked->update([
                'jumlah_total' => $data['jumlah_total'], 'jumlah_tersedia' => $data['jumlah_tersedia'],
                'versi_stok' => $locked->versi_stok + 1, 'stok_diperbarui_at' => now(),
            ]);
            $locked->riwayatStok()->create([
                'admin_id' => $admin->id, 'total_sebelum' => $before[0], 'tersedia_sebelum' => $before[1],
                'total_sesudah' => $locked->jumlah_total, 'tersedia_sesudah' => $locked->jumlah_tersedia,
                'versi_stok' => $locked->versi_stok, 'alasan' => $data['alasan'],
            ]);

            return $locked->load('kategori');
        }, 3);
    }

    public function hapus(Buku $buku): void
    {
        // Soft delete keeps audit records and prevents reuse of a book code.
        $buku->delete();
    }
}
