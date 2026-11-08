# Web Service Ketersediaan Buku Perpustakaan

**Acuan data terbaru:** dokumen kelompok yang Kakak berikan. Katalog awal sekarang memakai **10 buku BK001–BK010**, lengkap dengan penerbit dan tahun terbit. Rincian dan pemetaan field ada di [acuan dokumen](docs/acuan-dokumen.md).

Proyek UTS Pemrograman Web Service, **Kelompok 2**, kandidat release **v1.1.0**.

Repository kelompok: [bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2). Status pengunggahan dan persiapan release dicatat di [checklist release](docs/release.md).

## Anggota Kelompok 2

| No. | Nama | NIM |
| --- | --- | --- |
| 1 | Damiri | 23215020 |
| 2 | Mochamad Bilal Muadzin | 23215025 |
| 3 | Nofia Lindawati | 23215034 |
| 4 | Viryal Arliyani | 23215033 |
| 5 | Septina Asti Nabila | 23215005 |

Mochamad Bilal Muadzin mengerjakan program dengan bantuan AI. Nofia Lindawati menyusun laporan dan PPT berdasarkan hasil program. Usulan pekerjaan kelima anggota dan alur PR/review dijelaskan pada [rencana kontribusi](docs/kontribusi.md).

Daftar nama ini adalah identitas kelompok. Akses upload memerlukan undangan collaborator ke akun GitHub masing-masing; username empat anggota selain Bilal masih perlu diberikan.

Siswa mencari buku berdasarkan judul/penulis, memfilter kategori dan ketersediaan, melihat detail, rak, jumlah tersedia, dan waktu pembaruan. Petugas login, mengelola kategori/buku, dan mencatat perubahan stok. Peminjaman, pengembalian, denda, dan reservasi berada di luar lingkup.

## Membuka di komputer Bilal

Proyek UTS sudah dipisahkan ke folder Desktop `UTS Perpustakaan Kelompok 2`, terpisah dari website madrasah. Program Laravel berada di subfolder `perpustakaan`, database lokal di `.local-mysql`, dan dokumen di `Hasil UTS Kelompok 2`. Website madrasah lama dan ZIP cadangannya tetap berada di folder lama.

Buka folder `C:\Users\HP\OneDrive\Desktop\UTS Perpustakaan Kelompok 2\perpustakaan` di VS Code. Pada terminal PowerShell di folder program tersebut, jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mulai.ps1
```

Biarkan terminal tersebut terbuka. Kunjungi:

- Siswa: **http://127.0.0.1:8000**
- Petugas: **http://127.0.0.1:8000/petugas**
- Contoh API: **http://127.0.0.1:8000/api/v1/buku**

Email demo: `petugas@perpustakaan.test`. Saat akun pertama kali dibuat, kata sandinya berasal dari **DEMO_PASSWORD** pada `.env`. Jika kata sandi akun sudah diganti, gunakan kata sandi terbaru; nilai DEMO_PASSWORD tidak otomatis ikut berubah. Seeder tidak mengganti kata sandi petugas yang sudah ada. Jangan membagikan `.env`, kata sandi, atau token maupun memasukkannya ke repository, laporan, dan slide.

MariaDB khusus proyek berada di folder induk `.local-mysql`, mendengarkan **127.0.0.1:3307**, dengan database `perpustakaan` dan `perpustakaan_test`. Binary yang dipakai berasal dari `C:/RDMMadrasah/mysql/bin`; data dan pengaturan RDM port 3203 tidak diubah. Script mulai menjalankan instance terpisah ini bila belum aktif, kemudian menjalankan Laravel. Pada pemasangan lokal ini pengguna database adalah root tanpa kata sandi, hanya untuk instance pengembangan yang terikat loopback. Jika memakai server database sendiri, isi kredensial akun database sendiri pada `.env`.

Jika terminal masih berada di folder `UTS Perpustakaan Kelompok 2`, jalankan `cd .\perpustakaan` terlebih dahulu. `Ctrl+C` menghentikan server Laravel. Jika port 8000 sedang dipakai, hentikan terminal server lama terlebih dahulu. Laravel tidak dibuka dengan Live Server dan tidak dijalankan dengan klik dua kali file HTML.

## Teknologi dan berkas penting

Laravel 13.40.0, PHP 8.5.7 pada komputer ini, Composer 2.10.1, MariaDB 10.4.17 melalui driver MySQL, Node.js 24.16.0. Gunakan PHP 8.5 agar sesuai dependency lock. Ekstensi yang diperlukan: curl, openssl, mbstring, PDO, pdo_mysql, pdo_sqlite untuk tes cepat, dom, xml, fileinfo, dan zip. Git tersedia. CI juga menyiapkan pengujian MySQL 8.4.

Tampilan memakai Blade, CSS dan JavaScript lokal di `public/`; tampilan ini tidak memerlukan proses Vite atau CDN. Portal siswa mengambil koleksi menggunakan `fetch('/api/v1/...')`, bukan array buku statis. Paket dan skrip Vite bawaan Laravel dipertahankan bila kelak ingin mengembangkan asset bundling.

| Lokasi | Isi |
| --- | --- |
| `routes/api.php` | 15 endpoint API v1 |
| `app/Http/Requests` | Validasi input |
| `app/Http/Controllers/Api/V1` | Controller tipis |
| `app/Services` | Aturan bisnis dan transaksi |
| `app/Models`, `database/` | Model, relasi, migration, factory, seeder |
| `app/Adapters/OpenLibraryAdapter.php` | Integrasi API luar |
| `app/Http/Resources` | Bentuk data respons |
| `bootstrap/app.php` | Middleware dan error JSON seragam |
| `docs/openapi.yaml` | Kontrak OpenAPI 3.0.3, versi 1.1.0 |
| `docs/postman` | Collection dan environment contoh tanpa rahasia |
| `docs/bahan-laporan.md` | Bahan laporan/PPT dan ERD |
| `docs/hasil-pengujian.md` | Hasil pemeriksaan aktual |
| `docs/kontribusi.md` | Rencana kontribusi nyata anggota |

## Instalasi di komputer lain

1. Siapkan PHP 8.5, Composer, MySQL 8 atau MariaDB 10.4+, Git, dan Node.js 24. Pastikan layanan database aktif.
2. Dari folder proyek, jalankan:

```powershell
composer install --no-interaction
Copy-Item .env.example .env
php artisan key:generate
```

Jangan menjalankan `Copy-Item` tersebut pada pemasangan yang sudah memiliki `.env`. Gunakan `.env` yang sudah ada.

3. Buat database kosong melalui klien MySQL atau phpMyAdmin:

```sql
CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE perpustakaan_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Edit `.env`: isi `DB_CONNECTION=mysql`, host, port (biasanya 3306), `DB_DATABASE=perpustakaan`, username, password database, dan **DEMO_PASSWORD** pilihan sendiri minimal 12 karakter. `.env.example` sengaja tidak berisi kredensial maupun APP_KEY.
5. Jalankan:

```powershell
php artisan config:clear
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Seeder menambahkan 10 buku dokumen, 5 kategori, satu petugas dan audit awal. Judul, penulis, penerbit, tahun, kategori, total dan tersedia mengikuti dokumen kelompok sebagai data contoh tugas. Nomor rak merupakan contoh tambahan. Seeder bisa diulang tanpa menggandakan data; buku yang sudah diedit tidak ditimpa. Jangan gunakan `migrate:fresh` pada database aplikasi yang ingin dipertahankan.

## API dan aturan bisnis

Base URL: `http://127.0.0.1:8000/api/v1`. Versi route adalah `v1`; versi kontrak/release saat ini `1.1.0`. Kirim `Accept: application/json`, dan untuk body JSON kirim `Content-Type: application/json`. Endpoint petugas memerlukan `Authorization: Bearer <token>` dari login.

| Metode | Path | Akses | Hasil utama |
| --- | --- | --- | --- |
| POST | `/auth/login` | Publik | 200 token, 401 login salah |
| POST | `/auth/logout` | Petugas | 204, token dicabut |
| GET | `/buku` | Publik | 200 daftar/pencarian/filter/halaman |
| GET | `/buku/{id}` | Publik | 200 detail / 404 |
| POST | `/buku` | Petugas | 201 dan Location |
| PUT | `/buku/{id}` | Petugas | 200 edit data deskriptif |
| DELETE | `/buku/{id}` | Petugas | 204 arsip |
| PATCH | `/buku/{id}/stok` | Petugas | 200 / 422 / 409 |
| GET | `/buku/{id}/riwayat-stok` | Petugas | 200, 20 catatan per halaman |
| GET | `/kategori` | Publik | 200 daftar kategori |
| GET | `/kategori/{id}` | Publik | 200 detail / 404 |
| POST | `/kategori` | Petugas | 201 dan Location |
| PUT | `/kategori/{id}` | Petugas | 200 edit |
| DELETE | `/kategori/{id}` | Petugas | 204 / 409 jika digunakan |
| GET | `/metadata?q=...` | Publik | 200 normal atau fallback |

Daftar buku mendukung `q`, `kategori_id`, `tersedia=1` atau `0`, `sort=judul|penulis|jumlah_tersedia|stok_diperbarui_at`, `direction=asc|desc`, `per_page=1..50`, dan `page>=1`. Default 6 data, urutan judul naik. `q` mencari judul atau penulis. Contoh:

```text
/api/v1/buku?q=Andrea&tersedia=1&sort=judul&direction=asc&per_page=1&page=1
```

Penerbit dan tahun terbit wajib pada tambah/edit. Kode maksimal 20 karakter, judul 150, penulis/penerbit 100. Tahun 1901 sampai tahun berjalan (maksimal 2155).

Stok memenuhi `0 ≤ jumlah_tersedia ≤ jumlah_total`, jumlah total maksimal 1.000.000. Input tidak valid, kode/nama yang sudah dipakai, atau kategori tidak ada menghasilkan **422 VALIDATION_ERROR**. Stok dengan versi lama atau kategori yang masih dipakai menghasilkan **409 BUSINESS_CONFLICT**. Race pelanggaran relasi/keunikan pada database juga ditangani 409.

Setiap perubahan stok memakai **DB::transaction**, membaca ulang baris dengan **lockForUpdate**, memeriksa `versi_stok`, memperbarui stok dan menambah audit. Jika audit gagal, perubahan buku ikut rollback. Transaksi dicoba ulang hingga tiga kali untuk deadlock. Nomor versi mencegah petugas menimpa perubahan dari layar lama. Input stok adalah nilai akhir, bukan penambahan/pengurangan.

Contoh body stok (gunakan versi dari detail buku terkini):

```json
{"jumlah_total":10,"jumlah_tersedia":5,"versi_stok":1,"alasan":"Pengecekan rak"}
```

`PUT /buku/{id}` wajib mengirim kode, kategori, judul, penulis, penerbit, tahun terbit, dan rak. Jumlah dan versi stok dilarang di endpoint tersebut. Penghapusan buku memakai soft delete; riwayat tetap ada dan kode tidak digunakan ulang. Kategori yang dipakai buku aktif atau arsip tidak dapat dihapus.

Respons data dibungkus `data`; daftar berhalaman memiliki `links` dan `meta`. Error selalu berbentuk:

```json
{"error":{"code":"BUSINESS_CONFLICT","message":"Stok sudah diubah petugas lain. Muat ulang detail buku dan kirim versi_stok terbaru.","details":{}}}
```

Error validasi menyertakan rincian per field. 401 berarti perlu login; 404 data/endpoint tidak ditemukan; 405 metode salah; 429 terlalu banyak permintaan; 500 error server tanpa detail internal. Token di-hash SHA-256, berlaku 8 jam, dan satu token aktif per petugas. Login dibatasi 5/menit/IP dan metadata 10/menit/IP menggunakan limiter terpisah. Waktu API ISO-8601 UTC; tampilan menampilkan WIB.

## Integrasi Open Library

Mengikuti [dokumentasi resmi Search API](https://openlibrary.org/dev/docs/api/search). Adapter hanya mengambil metadata; stok sekolah tidak ditentukan oleh layanan luar. Hasil luar ditampilkan terpisah dan tidak otomatis disimpan menjadi buku.

- Endpoint penyedia: `GET https://openlibrary.org/search.json` dengan `q`, `limit=5`, dan daftar field tertentu.
- Konfigurasi di `config/services.php`: `OPENLIBRARY_BASE_URL`, `OPENLIBRARY_TIMEOUT` (default 4 detik per usaha), `OPENLIBRARY_CACHE_SECONDS` (3600), `OPENLIBRARY_USER_AGENT`, dan opsional `OPENLIBRARY_CA_BUNDLE`.
- Connect timeout 2 detik, maksimal 2 usaha, jeda 200 ms. Total waktu ketika retry bisa melebihi timeout satu usaha.
- Hasil sukses, termasuk hasil kosong, dicache. Kegagalan tidak dicache. Kegagalan HTTP/timeout/format menghasilkan 200 dengan `fallback=true`, `source=local`, `items=[]`; siswa tetap bisa mencari katalog lokal.

Di komputer ini PHP belum mempunyai CA bundle. Bundle resmi [curl/Mozilla](https://curl.se/docs/caextract.html) telah diunduh ke `.local/cacert.pem`, diverifikasi SHA-256 resmi, lalu dipasang melalui `OPENLIBRARY_CA_BUNDLE=.local/cacert.pem` pada `.env`. Verifikasi TLS tetap aktif. Berkas `.local` tidak dibagikan lewat Git. Pada komputer lain yang sudah memiliki CA sistem, biarkan variabel itu kosong. Bila mengalami cURL error 60, gunakan CA bundle resmi dan atur path relatif terhadap root proyek; jangan mematikan verifikasi sertifikat.

## Pengujian dan lint

```powershell
php artisan test --compact
php vendor/bin/phpunit --configuration phpunit.mysql.xml
npm.cmd ci --ignore-scripts
npm.cmd run lint:api
node --check public/js/perpustakaan.js
```

Tes cepat memakai SQLite in-memory dan melewati satu uji konkurensi khusus MySQL. Tes MySQL memakai database **perpustakaan_test**, bukan database aplikasi. Pastikan database ini sudah dibuat dan akun `.env` dapat mengaksesnya. Suite tes mengosongkan database tes. Uji dua proses menolak database yang namanya tidak berakhiran `_test`. CI menjalankan kedua suite, format PHP, dan lint API. Status Actions belum dapat diklaim sebelum di-push dan berjalan di GitHub.

Pengujian metadata memakai `Http::fake`, bukan tergantung internet. Tes mencakup cache, retry, timeout, respons rusak dan fallback. Uji rollback sengaja membuat penyimpanan audit gagal tanpa menyediakan endpoint sabotase pada aplikasi. Detail hasil aktual ada di `docs/hasil-pengujian.md`.

Redocly memakai ruleset recommended. Satu aturan `operation-4xx-response` dinonaktifkan dengan alasan di `redocly.yaml`: GET daftar kategori tanpa parameter tidak memiliki error 4xx bisnis sendiri. Aturan struktur, referensi, schema, dan aturan recommended lainnya tetap dijalankan. Tes juga membandingkan semua method/path API dengan kontrak.

## Postman dan alur demo

Import `docs/postman/collection.json` serta `docs/postman/environment.json` ke Postman. Pilih environment lokal; isi password **hanya pada nilai lokal** dan jangan export rahasia. Jalankan collection dari awal secara berurutan dengan Collection Runner. Collection berisi 22 skenario dan pemeriksaan respons; ID dan token diisi otomatis. Satu buku demo dibuat lalu diarsipkan, sehingga audit demo tetap tersimpan. Buku awal tidak dihapus atau diubah.

Collection sudah dijalankan dengan Newman saat verifikasi. Newman kemudian dilepas dari dependency proyek karena audit menunjukkan kerentanan pada dependensi turunannya; collection tetap kompatibel dengan Postman. Dependency npm yang dipertahankan telah diaudit ulang. Hindari memasukkan environment lokal atau output Newman mentah ke Git karena dapat memuat token dan password.

Urutan presentasi:

1. Buka katalog, cari Andrea, filter kategori/ketersediaan, pindah halaman, dan buka detail.
2. Login petugas dan tambah buku. Perlihatkan request Postman yang menghasilkan 201 beserta header Location.
3. Tunjukkan validasi stok tersedia melebihi total: 422. Akses ID yang tidak ada: 404.
4. Ambil versi stok buku, perbarui sekali, lalu kirim ulang versi lama: 409. Lihat stok tetap sama dan audit hanya bertambah untuk perubahan sukses.
5. Jalankan tes rollback: `php artisan test --filter=transaksi_rollback`.
6. Buka detail Laskar Pelangi dan klik metadata Open Library untuk koneksi normal. Respons berikutnya memakai cache.
7. Jalankan tes kegagalan: `php artisan test --filter=metadata_gagal` dan `php artisan test --filter=metadata_timeout`. Tampilkan `fallback=true` dari pengujian nyata awal bila diperlukan; koneksi jaringan bisa berubah saat demo.

## Release dan kontribusi anggota

`VERSION`, changelog, kontrak, collection, workflow dan checklist disiapkan untuk v1.1.0. Repository GitHub kelompok sudah dibuat dan remote `origin` sudah dihubungkan pada branch `main`. Unggahan awal tidak dihitung sebagai PR atau review anggota. Lihat `docs/release.md` dan `docs/kontribusi.md` untuk pekerjaan kolaborasi dan publikasi release yang masih perlu diselesaikan.

Salinan laporan Word dan presentasi sudah disusun di folder induk `Hasil UTS Kelompok 2`; Nofia Lindawati memiliki NIM 23215034. Kelompok tetap perlu meninjau isinya, melengkapi screenshot terminal asli, kontribusi, paraf, review dan PR nyata. Bahan program dan gambar pemeriksaan bukan pengganti kontribusi anggota tersebut.

## Memakai dokumen kelompok pada database lama

Penyesuaian pada komputer ini sudah dijalankan. Untuk pemasangan lama lainnya, gunakan `php artisan migrate` kemudian `php artisan buku:gunakan-data-dokumen`. Perintah membuat cadangan, mengarsipkan hanya contoh lama yang belum diubah, dan mengimpor 10 buku dokumen. Akun, kata sandi dan riwayat tetap disimpan. Jangan gunakan `migrate:fresh`. Lihat `docs/acuan-dokumen.md`.
