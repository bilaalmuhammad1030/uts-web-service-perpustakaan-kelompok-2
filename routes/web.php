<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'perpustakaan', ['portal' => 'siswa'])->name('katalog');
Route::view('/admin', 'perpustakaan', ['portal' => 'admin'])->name('admin');
