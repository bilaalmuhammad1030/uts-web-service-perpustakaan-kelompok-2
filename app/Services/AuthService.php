<?php

namespace App\Services;

use App\Models\Petugas;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function login(array $data): array
    {
        $petugas = Petugas::query()->where('email', $data['email'])->first();
        if (! $petugas || ! Hash::check($data['password'], $petugas->password)) {
            throw new AuthenticationException('Email atau kata sandi salah.');
        }
        $token = Str::random(64);
        $petugas->update(['token_hash' => hash('sha256', $token), 'token_expires_at' => now()->addHours(8)]);

        return ['token' => $token, 'token_type' => 'Bearer', 'expires_at' => $petugas->token_expires_at->toISOString(), 'petugas' => ['id' => $petugas->id, 'nama' => $petugas->nama]];
    }

    public function logout(Petugas $petugas): void
    {
        $petugas->update(['token_hash' => null, 'token_expires_at' => null]);
    }
}
