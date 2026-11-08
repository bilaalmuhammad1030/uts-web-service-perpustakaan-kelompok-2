<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok';

    public const UPDATED_AT = null;

    protected $fillable = ['buku_id', 'petugas_id', 'total_sebelum', 'tersedia_sebelum', 'total_sesudah', 'tersedia_sesudah', 'versi_stok', 'alasan'];

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class)->withTrashed();
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class);
    }
}
