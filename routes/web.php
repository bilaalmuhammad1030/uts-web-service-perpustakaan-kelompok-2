<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'perpustakaan', ['portal' => 'siswa'])->name('katalog');
Route::view('/petugas', 'perpustakaan', ['portal' => 'petugas'])->name('petugas');
