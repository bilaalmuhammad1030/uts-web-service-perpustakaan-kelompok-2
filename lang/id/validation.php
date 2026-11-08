<?php

return [
    'required' => ':attribute wajib diisi.', 'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa bilangan bulat.', 'email' => 'Format email tidak valid.',
    'unique' => ':attribute sudah digunakan.', 'exists' => ':attribute tidak ditemukan.',
    'in' => 'Pilihan :attribute tidak valid.', 'boolean' => ':attribute harus bernilai 0 atau 1.',
    'regex' => 'Format :attribute tidak valid.',
    'prohibited' => ':attribute tidak boleh dikirim melalui endpoint ini. Gunakan endpoint stok.',
    'min' => ['numeric' => ':attribute minimal :min.', 'string' => ':attribute minimal :min karakter.'],
    'max' => ['numeric' => ':attribute maksimal :max.', 'string' => ':attribute maksimal :max karakter.'],
    'lte' => ['numeric' => ':attribute harus kurang dari atau sama dengan :value.'],
    'attributes' => ['jumlah_tersedia' => 'Jumlah tersedia', 'jumlah_total' => 'Jumlah total', 'versi_stok' => 'Versi stok', 'kategori_id' => 'Kategori', 'kode_buku' => 'Kode buku', 'q' => 'Kata pencarian'],
];
