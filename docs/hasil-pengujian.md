# Hasil Pengujian Aktual

Pemeriksaan lokal pada **8 November 2026, WIB**. Ini hasil dari eksekusi pada komputer Bilal, bukan hasil GitHub Actions. Tidak ada klaim deployment publik atau release GitHub.

## Lingkungan

Windows, PHP 8.5.7, Composer 2.10.1, Git 2.55.0.windows.2, Node.js 24.16.0, Laravel 13.40.0, PHPUnit 12.5.43. Database aplikasi dan pengujian memakai MariaDB 10.4.17 melalui driver Laravel `mysql` pada instance lokal tersendiri `127.0.0.1:3307`. Database RDM pada port 3203 tidak diubah. MySQL 8.4 sudah dijalankan dalam workflow GitHub; lihat catatan CI di bawah.

## Ringkasan akhir setelah penyesuaian dokumen kelompok

Empat tes tambahan memeriksa penerbit/tahun, validasi dokumen, seluruh contoh data dan perlindungan data saat impor. Acuan: `acuan-dokumen.md`.

| Pemeriksaan | Hasil yang diamati |
| --- | --- |
| Suite default, SQLite in-memory | 30 kasus: **29 lulus, 1 dilewati**, 164 assertions |
| Suite feature MariaDB | **29 lulus**, 169 assertions, tidak ada yang dilewati |
| Dua proses pembaruan bersamaan | Satu sukses, satu konflik; versi akhir 2; hanya satu audit perubahan; nilai audit sesuai buku |
| Collection Postman melalui Newman | **22 request**, **60 assertions**, **0 gagal** |
| Interaksi browser Edge | **14 alur lulus**, **0 error JavaScript** |
| Ponsel 390 px | Tidak ada overflow horizontal pada pemeriksaan browser |
| Redocly lint | Kontrak valid, **0 error dan 0 warning** dengan konfigurasi yang disertakan |
| Kecocokan kontrak | Tes membandingkan **15 method/path** aktual dan versi kontrak dengan VERSION |
| Sintaks JavaScript | `node --check public/js/perpustakaan.js` berhasil |
| Format PHP | Laravel Pint berhasil, pemeriksaan lanjutan berstatus passed |
| Composer | `composer validate --strict` berhasil |
| Audit dependency npm akhir | **0 kerentanan** pada dependency yang dipertahankan |
| API eksternal nyata | Open Library berhasil, `source=openlibrary`, `fallback=false`, 5 item |
| Halaman dan data awal | Halaman siswa/petugas HTTP 200; 10 buku dokumen tetap aktif setelah demo diarsipkan |
| Script mulai ulang | Sudah diuji pada versi awal dengan 12 contoh: script berhasil menjalankan kembali server dan database. Sesudah penyesuaian dokumen, API aktif diverifikasi HTTP 200 dengan 10 buku |
| Git ignore | `.env`, `.local` termasuk environment/token, CA lokal dan SQLite diabaikan Git |

Satu tes dilewati pada SQLite karena membutuhkan penguncian baris dan dua proses/koneksi MySQL. Tes yang sama **benar-benar dijalankan dan lulus pada MariaDB**, bukan dianggap lulus dari hasil skip.

## Perintah pengujian

```powershell
php artisan test --compact
php artisan test --configuration=phpunit.mysql.xml --compact
npm.cmd run lint:api
node --check public/js/perpustakaan.js
php vendor/bin/pint --dirty --format agent
php vendor/bin/pint --format agent
composer validate --strict
```

Pada eksekusi akhir, runner Laravel menghasilkan ringkasan:

```json
{"tool":"phpunit","result":"passed","tests":30,"passed":29,"assertions":164,"duration_ms":3519,"skipped":1}
{"tool":"phpunit","result":"passed","tests":29,"passed":29,"assertions":169,"duration_ms":11486}
```

Waktu pengujian merupakan satu hasil eksekusi, dapat berbeda pada percobaan berikutnya. Jangan mengklaim dua suite ini sebagai 58 kasus unik; banyak kasus yang sama dijalankan dengan mesin database berbeda. Suite default memuat 27 tes LibraryApi, dua tes dasar Laravel, dan satu tes konkurensi khusus MySQL yang dilewati. Suite MySQL memuat 27 tes LibraryApi, satu tes halaman dasar, dan satu tes konkurensi.

## Catatan eksekusi GitHub Actions

Kode diunggah ke repository publik setelah persetujuan Bilal. Pada [workflow pertama](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/actions/runs/42480301529), seluruh 29 tes MySQL lulus dengan 169 assertions, termasuk uji dua proses. Namun perintah `php artisan test --configuration=phpunit.mysql.xml --compact` mengembalikan exit code 1 karena peringatan `Option --configuration cannot be used more than once`. Jadi workflow pertama gagal meskipun assertions lulus.

Workflow dan panduan README diperbaiki untuk memanggil runner langsung: `php vendor/bin/phpunit --configuration phpunit.mysql.xml`. Ini menjalankan suite yang sama tanpa argumen konfigurasi ganda dan tanpa menonaktifkan tes atau peringatan. Perintah Artisan pada bagian log lokal di atas dipertahankan sebagai catatan eksekusi sebelumnya. Untuk menjalankan suite MySQL berikutnya, gunakan perintah PHPUnit langsung tersebut. Hasil akhir CI harus dibaca pada tab Actions untuk commit terbaru.

Verifikasi perintah pengganti pada MariaDB lokal: exit code 0, 29 tes lulus, 169 assertions, durasi 13,856 detik pada eksekusi ini. Percobaan awal dari sandbox dihentikan karena akses koneksi lokal tidak berjalan; hasil lulus berasal dari pengulangan dengan akses lingkungan pengguna.

## Cakupan tes feature

`tests/Feature/LibraryApiTest.php` mencakup:

1. Daftar, pencarian penulis, kategori, ketersediaan, urutan dan pagination.
2. Detail buku: kategori, rak, jumlah, versi, waktu.
3. Pembuatan buku 201 dengan Location dan audit awal.
4. Stok melebihi total atau negatif ditolak 422.
5. Kode unik dan kategori harus ada.
6. Data/endpoint tidak ditemukan 404 dengan JSON seragam.
7. Pengelolaan memerlukan token petugas, 401.
8. Login salah, login benar, logout dan token kedaluwarsa.
9. Stok dan riwayat berhasil disimpan bersama.
10. Versi lama ditolak 409 tanpa perubahan data.
11. Validasi stok 422 dibedakan dari konflik 409.
12. Rollback stok, total, versi dan riwayat ketika penyimpanan audit sengaja gagal.
13. Pembuatan buku juga rollback ketika audit awal gagal.
14. CRUD kategori serta 409 ketika kategori dipakai.
15. Edit buku tidak bisa melewati endpoint stok.
16. Arsip buku mempertahankan audit dan menghilangkan detail publik.
17. Batas parameter daftar dan whitelist pengurutan.
18. Metadata sukses, normalisasi, cache dan parameter permintaan penyedia melalui Http::fake.
19. 503 dicoba ulang, fallback tidak dicache, permintaan berikutnya dapat berhasil.
20. Timeout dan format rusak menghasilkan fallback melalui Http::fake.
21. Halaman siswa dan petugas dapat dibuka.
22. Seeder berisi 10 buku dokumen dan idempoten.
23. Seluruh route API dan versi rilis sesuai kontrak OpenAPI.
24. Penerbit/tahun disimpan dan ditampilkan melalui API.
25. Penerbit wajib, tahun valid, dan panjang field mengikuti dokumen.
26. Seluruh 10 buku sesuai data dokumen; pengulangan seeder tidak menimpa edit.
27. Impor hanya mengarsipkan contoh lama yang belum diubah, mempertahankan audit dan data pengguna.

`StockConcurrencyTest` menyiapkan dua proses PHP dengan koneksi terpisah yang mengirim versi stok 1 untuk buku yang sama pada waktu awal yang sama. Hasil diperiksa sebagai satu sukses dan satu konflik; tidak ada pembaruan tertimpa atau audit ganda. Database uji wajib berakhiran `_test` dan data aplikasi tidak dipakai.

## Collection Postman

Collection 22 langkah sudah dijalankan ke server Laravel lokal menggunakan Newman 6.2.3. Semua request dan pemeriksaan respons berhasil. Cakupan: login, CRUD kategori/buku, pencarian/filter/pagination, metadata, 422, 404, dua jenis 409, konsistensi stok/audit, arsip, logout dan token tercabut.

Output mentah Newman disimpan lokal dan tidak dibagikan karena dapat memuat password/token. Eksekusi ulang setelah penyesuaian dokumen: 1 iterasi, 22 request, 22 test scripts, 60 assertions, seluruh kolom failed bernilai 0; durasi 6,1 detik. Pembuatan/edit buku mencakup penerbit dan tahun terbit. Metadata berhasil melalui Open Library. Pada eksekusi awal sebelum penyesuaian (10,4 detik), metadata mendapat fallback nyata akibat CA PHP belum tersedia; pemeriksaan collection memang menerima respons normal atau fallback yang sesuai kontrak.

Setelah uji awal, Newman dilepas dari dependency proyek karena audit menemukan 19 kerentanan pada rantai dependency alat tersebut. Audit ulang dependency yang dipertahankan menghasilkan 0 kerentanan. Uji ulang dokumen memakai Newman sementara di cache melalui npm exec, tanpa mengubah dependency aplikasi. Collection tetap dapat diimpor dan dijalankan di Postman; dependency alat itu tidak dipertahankan untuk menjalankan aplikasi.

## Pemeriksaan browser dan screenshot

Edge dijalankan tanpa jendela terlihat dengan profile QA terpisah. Interaksi yang berhasil: katalog 6 kartu dari API, pencarian Laskar, detail, viewport ponsel 390 px, login, tambah buku, edit rak, perubahan stok, riwayat, arsip buku, tambah kategori, edit kategori, hapus kategori dan logout. Jumlah error JavaScript: 0.

Pada percobaan awal, pemeriksaan otomatis terlalu cepat membaca formulir lama ketika dialog baru masih dimuat. Pemeriksaan diperbaiki agar menunggu dialog benar-benar terbuka; isi dialog juga dibersihkan saat ditutup. Pengujian ulang semua alur tersebut berhasil. Simbol stok dan penataan ilustrasi ponsel kemudian dirapikan berdasarkan screenshot, tanpa perubahan logika program.

Screenshot aktual disertakan pada `docs/screenshots`. Buku demo pengujian diarsipkan, auditnya tetap disimpan. Jumlah pada sebagian screenshot selama pengujian dapat mencakup buku demo, sedangkan keadaan akhir katalog berisi 10 buku dokumen aktif.

## Integrasi eksternal: nyata dan simulasi

- **Nyata, sebelum perbaikan:** permintaan melalui API proyek menghasilkan fallback. Diagnosis PHP menunjukkan cURL error 60 (CA belum dikonfigurasi).
- **Nyata, sesudah perbaikan:** bundle CA resmi curl/Mozilla diunduh dan SHA-256 dicocokkan dengan hash resmi; adapter diberi path CA lokal. `GET /api/v1/metadata?q=Laskar%20Pelangi` berhasil 200, `source=openlibrary`, `fallback=false`, `cached=false`, 5 item. Item pertama Laskar Pelangi, Andrea Hirata, 2005, ISBN 9789793062792. Verifikasi sertifikat tetap aktif.
- **Simulasi terkontrol:** Http::fake menguji respons sukses/cache, dua 503 dan retry, ConnectionException, format rusak, serta pemulihan setelah kegagalan. Ini bukan bukti bahwa penyedia benar-benar mengalami gangguan tersebut.

## Batas pemeriksaan

Kode awal sudah diunggah melalui commit `a9c10ad`. GitHub Actions telah dipicu: [eksekusi pertama](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/actions/runs/42480301529). Hasil eksekusi dan commit terbaru dapat diperiksa pada tab Actions; angka hasil lokal di atas tidak otomatis menjadi angka hasil CI. Tag/release v1.1.0 belum dipublikasikan. Belum ada PR, merge, atau review anggota; anggota perlu membuat kontribusi nyata sesuai `kontribusi.md`. Belum dilakukan uji beban besar, audit keamanan menyeluruh, pemeriksaan aksesibilitas menyeluruh, atau deployment produksi. Aplikasi saat ini ditujukan untuk demo UTS lokal sesuai lingkup yang diminta.
