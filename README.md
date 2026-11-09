# Web Service Ketersediaan Buku Perpustakaan

[![Pemeriksaan proyek v1.1.0](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/actions/workflows/tests.yml)

**UTS Pemrograman Web Service · Kelompok 2**

Laravel · MySQL/MariaDB · REST API v1

Membantu siswa menemukan buku, memeriksa stok, dan mengetahui lokasi rak sebelum datang ke perpustakaan. Admin mengelola koleksi melalui satu sumber data.

![Tampilan katalog perpustakaan](docs/screenshots/katalog-desktop.png)

## Demo online untuk presentasi

**[Buka Demo Website](https://match-simpsons-referred-mother.trycloudflare.com)**

[Katalog siswa](https://match-simpsons-referred-mother.trycloudflare.com) · [Login petugas](https://match-simpsons-referred-mother.trycloudflare.com/petugas) · [API buku](https://match-simpsons-referred-mother.trycloudflare.com/api/v1/buku)

Anggota kelompok dapat membuka link di atas dari browser tanpa memasang aplikasi. Link bersifat publik; fitur petugas tetap memerlukan login. Kata sandi dibagikan secara pribadi, bukan di GitHub.

Link sementara ini hanya aktif selama komputer penyaji, server Laravel, koneksi internet, dan Cloudflare Tunnel tetap berjalan. Jika tunnel dijalankan ulang, alamat dapat berubah; perbarui link di bagian ini sebelum presentasi berikutnya.

## Fitur utama

- **Siswa:** pencarian judul/penulis, filter kategori, detail buku, stok dan lokasi rak.
- **Admin:** login, kelola buku/kategori, pembaruan stok dan riwayat perubahan.
- **API:** 15 endpoint berversi, pagination, validasi dan respons JSON seragam.
- **Konsistensi:** transaksi, penguncian stok, deteksi konflik dan rollback.
- **Metadata:** Open Library dengan timeout, retry, cache dan fallback.

Fokus pada ketersediaan buku. Peminjaman, pengembalian, denda dan reservasi berada di luar lingkup.

## Anggota Kelompok 2

| Nama | NIM |
| --- | --- |
| Damiri | 23215020 |
| Mochamad Bilal Muadzin | 23215025 |
| Nofia Lindawati | 23215034 |
| Viryal Arliyani | 23215033 |
| Septina Asti Nabila | 23215005 |

 [Rencana kontribusi](docs/kontribusi.md).

## Jalankan aplikasi

Pada komputer Bilal, buka folder `UTS Perpustakaan Kelompok 2/perpustakaan`, lalu jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mulai.ps1
```

[Portal siswa](http://127.0.0.1:8000) · [Login admin](http://127.0.0.1:8000/admin) · [API buku](http://127.0.0.1:8000/api/v1/buku)

Login admin: Email `adminperpustakaan@gmail.com` (atau username `Admin`). Gunakan kata sandi akun terbaru.

<details>
<summary><strong>Instalasi di komputer lain</strong></summary>

Siapkan PHP 8.5, Composer, MySQL/MariaDB dan Node.js 24 untuk lint API.

```powershell
composer install --no-interaction
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
```

Buat database `perpustakaan` dan `perpustakaan_test`. Atur `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, serta `DEMO_PASSWORD` minimal 12 karakter pada `.env`. Gunakan file `.env` yang sudah ada jika aplikasi pernah dipasang.

```powershell
php artisan migrate --seed
php artisan serve
```

Seeder menyediakan 10 contoh buku. Kata sandi awal berasal dari `DEMO_PASSWORD`; seeder tidak mengganti kata sandi akun yang sudah ada. [Panduan lengkap](docs/panduan-teknis.md#instalasi-di-komputer-lain).

</details>

## Jalankan Aplikasi Secara Lokal

Pada komputer lokal, buka folder proyek dan jalankan perintah:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mulai.ps1
```

Aplikasi otomatis berjalan di **`http://127.0.0.1:8000`**.

<details>
<summary><strong>Panduan Instalasi di Komputer Lain</strong></summary>

Siapkan PHP 8.4/8.5, Composer, MySQL/MariaDB, dan Node.js 24 untuk linting API.

```powershell
composer install --no-interaction
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
```

Buat basis data `perpustakaan` dan `perpustakaan_test`. Atur `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, serta `DEMO_PASSWORD` pada file `.env`.

```powershell
php artisan migrate --seed
php artisan serve
```

Seeder otomatis menyediakan 10 data buku lengkap dengan sampul dan kategori. [Panduan teknis lengkap](docs/panduan-teknis.md#instalasi-di-komputer-lain).

</details>

## Uji Otomatis & Verifikasi Kualitas

```powershell
# Jalankan suite pengujian otomatis PHPUnit
php artisan test --compact
php vendor/bin/phpunit --configuration phpunit.mysql.xml

# Linting kode dan spesifikasi OpenAPI
npm ci --ignore-scripts
npm run lint:api
node --check public/js/perpustakaan.js
```

- **Hasil Uji PHPUnit:** `PASSING` (30 tests, 164 assertions pada SQLite; 29 tests, 169 assertions pada MySQL).
- **Hasil Uji Newman / Postman:** `PASSING` (22 skenario lulus, 60 assertions, 0 failures). [Bukti Pengujian Aktual](docs/hasil-pengujian.md).
- **Hasil Linting Kontrak API:** `0 errors, 0 warnings` (Lolos validasi OpenAPI 3.0.3).

## Dokumentasi Proyek

- [Spesifikasi Kontrak OpenAPI (YAML)](docs/openapi.yaml)
- [Dokumen Kontrak API Word (.docx)](docs/KONTRAK_API_OPENAPI.docx) / [Root .docx](KONTRAK_API_OPENAPI.docx)
- [Koleksi Postman & Bukti Pengujian](docs/postman/collection.json)
- [Panduan Teknis Instalasi & Konfigurasi](docs/panduan-teknis.md)
- [Bahan Laporan & Presentasi](docs/bahan-laporan.md)
- [Checklist Release Versi 1.1.0](docs/release.md)