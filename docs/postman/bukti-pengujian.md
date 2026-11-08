# Bukti Pengujian Postman

Waktu: Minggu, 08 November 2026 pukul 22.40.03 WIB.

Koleksi dijalankan dengan **Newman 6.2.1 (runner koleksi Postman)** terhadap API Laravel lokal. **22 skenario lulus, 60 assertions, 0 kegagalan**.

Hasil request HTTP nyata melalui Newman, bukan screenshot aplikasi Postman Desktop. Lingkungan: Laravel lokal port 8011, database SQLite uji terpisah, data contoh dari seeder proyek. Akun dan database aplikasi utama tidak diubah. Password dan token disamarkan. Metadata dapat memakai sumber eksternal atau fallback sesuai respons aktual.

## Berkas bukti

- [Laporan PDF](bukti-pengujian.pdf): siap dilampirkan pada laporan UTS.
- [Laporan HTML](bukti-pengujian.html): ringkasan dan rincian request, respons, serta assertions setiap skenario; unduh lalu buka di browser.
- [Hasil JSON](bukti-pengujian.json): data hasil aktual yang sudah disamarkan.
- [Collection](collection.json) dan [environment contoh](environment.json): impor ke Postman untuk mengulang.

## Hasil per skenario

| No. | Skenario | Metode | HTTP aktual | Assertions | Hasil |
| --- | --- | --- | --- | --- | --- |
| 1 | 01 · Login petugas | POST | 200 | 3 | LULUS |
| 2 | 02 · Daftar kategori | GET | 200 | 3 | LULUS |
| 3 | 03 · Buat kategori 201 + Location | POST | 201 | 3 | LULUS |
| 4 | 04 · Detail kategori | GET | 200 | 3 | LULUS |
| 5 | 05 · Edit kategori | PUT | 200 | 3 | LULUS |
| 6 | 06 · Buat buku dan audit awal | POST | 201 | 3 | LULUS |
| 7 | 07 · Daftar, cari, filter, urut, pagination | GET | 200 | 3 | LULUS |
| 8 | 08 · Detail buku | GET | 200 | 3 | LULUS |
| 9 | 09 · Metadata eksternal atau fallback | GET | 200 | 3 | LULUS |
| 10 | 10 · Validasi stok 422 | PATCH | 422 | 3 | LULUS |
| 11 | 11 · Data tidak ditemukan 404 | GET | 404 | 3 | LULUS |
| 12 | 12 · Kategori dipakai konflik 409 | DELETE | 409 | 3 | LULUS |
| 13 | 13 · Edit data deskriptif buku | PUT | 200 | 3 | LULUS |
| 14 | 14 · Transaksi stok berhasil | PATCH | 200 | 3 | LULUS |
| 15 | 15 · Versi lama konflik 409 | PATCH | 409 | 3 | LULUS |
| 16 | 16 · Konflik tidak mengubah stok | GET | 200 | 3 | LULUS |
| 17 | 17 · Audit konsisten dengan stok | GET | 200 | 3 | LULUS |
| 18 | 18 · Arsipkan buku demo | DELETE | 204 | 1 | LULUS |
| 19 | 19 · Buku arsip tidak ditampilkan | GET | 404 | 3 | LULUS |
| 20 | 20 · Hapus kategori kosong | DELETE | 204 | 1 | LULUS |
| 21 | 21 · Logout | POST | 204 | 1 | LULUS |
| 22 | 22 · Token tercabut ditolak 401 | POST | 401 | 3 | LULUS |

## Mengulang di Postman

1. Jalankan Laravel dan database proyek.
2. Impor collection.json dan environment.json, lalu pilih environment tersebut.
3. Isi password akun petugas pada environment lokal. Jangan unggah password atau token.
4. Sesuaikan base_url (bawaan http://127.0.0.1:8000/api/v1).
5. Jalankan seluruh collection secara berurutan menggunakan Collection Runner.
6. Jika dosen mensyaratkan screenshot aplikasi Postman, ambil screenshot Runner dan minimal 10 request yang menampilkan URL, request, respons, serta Test Results. Dokumen ini menyertakan bukti eksekusi Newman dan tidak mengaku sebagai screenshot Postman Desktop.

Pengulangan membuat buku dan kategori demo; buku demo kemudian diarsipkan, kategori demo dihapus, dan token dicabut pada akhir collection. Sepuluh buku utama tidak diedit oleh collection.
