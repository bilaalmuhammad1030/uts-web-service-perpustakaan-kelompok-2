<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function login(array $data): array
    {
        $identifier = $data['username'] ?? $data['email'] ?? null;
        $admin = Admin::query()
            ->where('email', $identifier)
            ->orWhere('nama', $identifier)
            ->first();
        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            throw new AuthenticationException('Username atau kata sandi salah.');
        }
        $token = Str::random(64);
        $admin->update(['token_hash' => hash('sha256', $token), 'token_expires_at' => now()->addHours(8)]);

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $admin->token_expires_at->toISOString(),
            'admin' => ['id' => $admin->id, 'nama' => $admin->nama],
        ];
    }

    public function logout(Admin $admin): void
    {
        $admin->update(['token_hash' => null, 'token_expires_at' => null]);
    }
}
