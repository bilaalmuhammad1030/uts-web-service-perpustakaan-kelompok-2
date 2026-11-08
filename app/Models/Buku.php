<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buku';

    protected $fillable = ['kategori_id', 'kode_buku', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'deskripsi', 'isbn', 'lokasi_rak', 'jumlah_total', 'jumlah_tersedia', 'versi_stok', 'stok_diperbarui_at'];

    protected function casts(): array
    {
        return ['stok_diperbarui_at' => 'datetime', 'jumlah_total' => 'integer', 'jumlah_tersedia' => 'integer', 'versi_stok' => 'integer', 'tahun_terbit' => 'integer'];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }
}
