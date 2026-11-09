<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BukuController;
use App\Http\Controllers\Api\V1\KategoriController;
use App\Http\Controllers\Api\V1\MetadataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:admin-login');
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
    Route::get('kategori', [KategoriController::class, 'index']);
    Route::get('kategori/{kategori}', [KategoriController::class, 'show'])->name('kategori.show');
    Route::get('metadata', MetadataController::class)->middleware('throttle:metadata');
    Route::middleware('admin')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('buku', [BukuController::class, 'store']);
        Route::put('buku/{buku}', [BukuController::class, 'update']);
        Route::delete('buku/{buku}', [BukuController::class, 'destroy']);
        Route::patch('buku/{buku}/stok', [BukuController::class, 'stok']);
        Route::get('buku/{buku}/riwayat-stok', [BukuController::class, 'riwayat']);
        Route::post('kategori', [KategoriController::class, 'store']);
        Route::put('kategori/{kategori}', [KategoriController::class, 'update']);
        Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy']);
    });
});
