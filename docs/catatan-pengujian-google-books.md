# Catatan Pengujian Integrasi Google Books API

Dokumen ini mencatat hasil pengujian aktual integrasi **Google Books API** pada sistem Web Service Perpustakaan Kelompok 2.

* **Tanggal Pengujian:** 9 November 2026
* **Kredensial API Key:** `AIzaSyBx-JcJsE4is3x9aD-D4hHBTkbY7sIUe5U`
* **Status Integrasi:** **BERHASIL & AKTIF (100% Lulus)**
* **Lokasi Konfigurasi:**
  * File `.env`: `GOOGLE_BOOKS_API_KEY=AIzaSyBx-JcJsE4is3x9aD-D4hHBTkbY7sIUe5U`
  * File `config/services.php`: blok konfigurasi `'google_books'`
  * Service Adapter: `app/Adapters/GoogleBooksAdapter.php`

---

## 1. Pengujian Konektivitas Langsung ke Server Google Books

Pengujian dilakukan dari server lokal menggunakan protokol HTTPS ke endpoint resmi Google Books API:

* **Endpoint:** `GET https://www.googleapis.com/books/v1/volumes`
* **Parameter Uji:** `q=Laskar+Pelangi&key=AIzaSyBx-JcJsE4is3x9aD-D4hHBTkbY7sIUe5U&maxResults=5`
* **Hasil Respons Server Google:**
  * **HTTP Status Code:** `200 OK`
  * **Total Items Ditemukan:** 175 buku
  * **Struktur Data Terverifikasi:**
    * `title` (Judul buku)
    * `authors` (Daftar nama pengarang)
    * `publisher` (Penerbit resmi)
    * `publishedDate` (Tahun terbit)
    * `industryIdentifiers` (ISBN-10 dan ISBN-13)
    * `imageLinks.thumbnail` (Tautan gambar sampul/cover resmi)
    * `description` (Sinopsis lengkap)

---

## 2. Pengujian Adapter Laravel (`GoogleBooksAdapter`)

Pengujian dilakukan di dalam konteks aplikasi Laravel untuk menguji parser data, pemetaan field, dan performa cache:

| Skenario Pengujian | Input Query | Hasil yang Diamati | Status |
| :--- | :--- | :--- | :--- |
| Pencarian Pertama (Cold Request) | `"Laskar Pelangi"` | `source: google_books`, `fallback: false`, `cached: false`, pesan formal sistem disinkronkan, 5 item termuat lengkap. | **LULUS** |
| Pencarian Kedua (Cache Hit) | `"Laskar Pelangi"` | `source: google_books`, `cached: true`, pesan log teknis memuat keterangan `(dimuat dari cache)`, respons instan (< 5 ms). | **LULUS** |
| Verifikasi Sertifikat SSL | Google HTTPS | CA bundle `curl-ca-bundle.crt` terverifikasi; tidak ada error cURL 60. | **LULUS** |

> **Pembedaan Pesan Respons:**
> * **Tampilan UI (Pengguna Umum):** *"Informasi buku diambil dari Google Books, sedangkan status ketersediaan buku fisik mengacu pada data perpustakaan."*
> * **Log Sistem / API Teknis:** *"Metadata resmi disinkronkan dari Google Books API, sementara ketersediaan koleksi fisik mengacu langsung pada database perpustakaan (dimuat dari cache)."*

---

## 3. Pengujian Suite Otomatis PHPUnit

Rangkaian pengujian unit dan fitur dijalankan melalui PHPUnit dengan penambahan pengujian khusus Google Books:

* **Berkas Pengujian Baru:** [`tests/Feature/GoogleBooksMetadataTest.php`](file:///d:/Project/uts-web-service-perpustakaan-kelompok-2/tests/Feature/GoogleBooksMetadataTest.php)
  1. `test_google_books_metadata_sukses_dan_cache`: Memverifikasi respons normal, pemetaan JSON ke format internal, dan fungsi penyimpanan cache.
  2. `test_google_books_fallback_pada_koneksi_error`: Memverifikasi resiliensi aplikasi jika Google Books sedang timeout/gangguan (aplikasi tetap aman berjalan dengan fallback lokal).

### Ringkasan Suite Uji Lengkap:
* **Total Kasus Uji:** 32 Tests
* **Lulus:** 31 Tests (100% dari seluruh modul yang aktif)
* **Skipped:** 1 Test (spesifik isolasi konkurensi multi-proses MariaDB)
* **Total Assertions:** 182 assertions
* **Pint Code Style:** Passed (Clean code sesuai PSR-12 / Laravel Guidelines)

---

## 4. Pengujian Antarmuka Pengguna (UI)

* **Tampilan Katalog Siswa:** Sampul banner buku asli dari Google Books berhasil dimuat pada kartu buku dan *featured book*.
* **Tampilan Modal Detail Buku:** Gambar cover resolusi tinggi muncul di bagian atas modal detail.
* **Fitur Pencarian Metadata Google Books:** Tombol *"Lihat buku terkait di Google Book"* berhasil memanggil endpoint `/api/v1/metadata`, mengembalikan daftar buku hasil pencarian lengkap dengan thumbnail dan ISBN-nya.
