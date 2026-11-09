<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = ['nama', 'email', 'password', 'token_hash', 'token_expires_at'];

    protected $hidden = ['password', 'token_hash'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'token_expires_at' => 'datetime'];
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }
}
