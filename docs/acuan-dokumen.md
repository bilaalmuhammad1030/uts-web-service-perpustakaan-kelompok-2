# Penyesuaian berdasarkan dokumen kelompok

Acuan pengguna: **Data dan Pengelolaan Buku Perpustakaan (1).docx**, dibaca pada 8 November 2026. Isi tabel contoh disalin ke `database/seeders/data/buku-dokumen.json`. Data ini diperlakukan sebagai data contoh tugas dari dokumen, bukan hasil verifikasi katalog penerbit.

## Data yang dipakai

| Kode | Judul | Penerbit | Tahun | Kategori | Stok | Tersedia |
| --- | --- | --- | --- | --- | --- | --- |
| BK001 | Laskar Pelangi | Bentang Pustaka | 2005 | Novel | 8 | 5 |
| BK002 | Bumi | Gramedia Pustaka Utama | 2014 | Novel | 10 | 7 |
| BK003 | Negeri 5 Menara | Gramedia Pustaka Utama | 2009 | Novel | 7 | 4 |
| BK004 | Bumi Manusia | Lentera Dipantara | 1980 | Novel | 6 | 3 |
| BK005 | Atomic Habits | Gramedia Pustaka Utama | 2019 | Pengembangan Diri | 9 | 6 |
| BK006 | Filosofi Teras | Kompas | 2018 | Pengembangan Diri | 8 | 5 |
| BK007 | Algoritma dan Pemrograman | Informatika | 2018 | Teknologi | 5 | 3 |
| BK008 | Basis Data | Informatika | 2018 | Teknologi | 6 | 4 |
| BK009 | Matematika Dasar | Erlangga | 2017 | Pendidikan | 5 | 2 |
| BK010 | Ensiklopedia Sains | Bhuana Ilmu Populer | 2020 | Ensiklopedia | 4 | 4 |

Penulis juga mengikuti dokumen. Dokumen tidak memberikan nomor rak tiap buku; rak contoh A-01 sampai E-01 ditambahkan untuk memenuhi fitur lokasi rak. Tahun dan penerbit wajib pada tambah/edit buku. Batas input mengikuti dokumen: kode 20, judul 150, penulis dan penerbit masing-masing 100 karakter. Tahun berupa bilangan bulat 1901 sampai tahun berjalan, maksimal 2155. Kolom tambahan nullable pada migration agar data lama tidak diberi tahun/penerbit fiktif; buku lama yang diedit perlu melengkapi kedua field tersebut. Lebar kolom lama tidak diperkecil agar data lama tetap aman; batas input diterapkan oleh Form Request.

## Pemetaan nama dan keputusan implementasi

| Istilah rancangan dokumen | Implementasi Laravel |
| --- | --- |
| `tb_buku`, BUKU | Tabel `buku`, model Buku |
| `id_buku` | `buku.id` |
| `id_kategori` | `buku.kategori_id` |
| `nama_kategori` | `kategori.nama` |
| `stok` | `jumlah_total` |
| `tersedia` | `jumlah_tersedia` |
| `penerbit`, `tahun_terbit` | Field dengan nama yang sama |
| `jumlah_perubahan` | Dapat dihitung dari tersedia_sesudah − tersedia_sebelum (atau total_sesudah − total_sebelum untuk perubahan total) |
| `stok_akhir` | total_sesudah / tersedia_sesudah; keduanya disimpan agar tidak ambigu |
| `waktu_update` | `riwayat_stok.created_at` dan `buku.stok_diperbarui_at` |

Nama fisik Laravel dipertahankan supaya API dan relasi yang sudah berjalan tetap konsisten. Empat tabel tetap mengikuti ERD pengembangan dokumen serta persyaratan awal dosen. Riwayat menyimpan nilai sebelum dan sesudah, identitas Admin, alasan, dan versi.

Tambah/edit buku memuat penerbit dan tahun. Detail siswa dan tabel Admin menampilkan keduanya. Pada edit buku, jumlah tetap diperbarui melalui tombol **Stok** agar transaksi dan pemeriksaan versi tidak dilewati. Konfirmasi hapus tetap muncul. Hapus menggunakan arsip (soft delete) sehingga catatan audit tidak hilang.

## Pembaruan data lokal yang sudah dilakukan

Migration tambahan telah dijalankan tanpa mengosongkan database. Perintah `php artisan buku:gunakan-data-dokumen` telah membuat cadangan JSON di `.local/sebelum-impor-dokumen-20261108-112946-nnNTBb.json`, lalu mengarsipkan 12 contoh lama yang cocok dengan seluruh data awal dan belum pernah diubah. Sepuluh buku dokumen menjadi katalog aktif. Riwayat stok lama, kategori lama yang masih direferensikan arsip, akun Admin dan kata sandi tetap disimpan. Karena kategori lama dipertahankan, jumlah kategori pada database yang diperbarui bisa lebih dari lima; pemasangan baru mempunyai lima kategori dokumen.

Perintah impor dapat diulang dengan aman. Kode dokumen yang sudah ada atau sudah diarsipkan tidak ditimpa/dihidupkan kembali. Contoh lama yang sudah diedit dipertahankan; catatan lain milik pengguna tidak dihapus. Cadangan lokal tidak masuk Git dan tidak berisi password maupun token Admin.

Seeder pemasangan baru langsung memakai 10 buku dokumen. Untuk database lama, jalankan migration kemudian perintah impor tersebut; jangan menjalankan `migrate:fresh` pada data aplikasi.

Kontrak OpenAPI, collection Postman, factory, pengujian dan bahan laporan telah disesuaikan. Hasil akhir tercatat di `hasil-pengujian.md`.

