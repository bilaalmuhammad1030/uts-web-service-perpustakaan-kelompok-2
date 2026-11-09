# Rencana kontribusi nyata Kelompok 2

Anggota: Damiri (23215020), Mochamad Bilal Muadzin (23215025), Nofia Lindawati (23215034), Viryal Arliyani (23215033), dan Septina Asti Nabila (23215005).

Repository: https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2.

Bilal adalah pemilik repository dengan akun `bilaalmuhammad1030`. Empat anggota lain akan diundang sebagai collaborator dengan akses menulis setelah username GitHub masing-masing diberikan. Undangan harus diterima dari akun anggota sendiri. Akses menulis memungkinkan anggota mengunggah branch dan membuat PR; akses ini belum menjadi bukti kontribusi.

Dokumen ini adalah usulan pekerjaan, bukan bukti kontribusi GitHub. Belum ada PR, review, merge, atau release GitHub yang dibuat oleh asisten.

## Usulan dua PR per anggota

Rencana berikut berlaku untuk pekerjaan berikutnya. Jangan mengunggah ulang implementasi yang sudah selesai sebagai karya baru atau memisahnya semata-mata untuk memenuhi jumlah PR. Setiap PR harus menghasilkan perubahan yang bermanfaat dan dapat diperiksa.

| Anggota | PR pertama | PR kedua | Minimal satu review pada PR milik |
| --- | --- | --- | --- |
| Bilal | Perbaikan API dari temuan pengujian anggota, disertai tes regresi untuk masalah yang benar-benar ditemukan. | Perbaikan login/konfigurasi keamanan dari hasil review, disertai alasan dan pemeriksaan yang sesuai. | Nofia |
| Nofia | Tinjau dan revisi laporan: cocokkan ERD, endpoint, hasil tes serta referensi dengan program yang dijalankan sendiri. | Tinjau dan revisi PPT: tambahkan screenshot aktual, pembagian pembicara, serta alur demo yang sudah dicoba. | Viryal |
| Viryal | Periksa pencarian/filter dengan keyboard; perbaiki masalah penggunaan atau aksesibilitas yang ditemukan. | Periksa tampilan ponsel dan detail buku; perbaiki masalah tata letak yang ditemukan, disertai screenshot sebelum/sesudah. | Septina |
| Septina | Verifikasi migration, foreign key dan data awal buku; perbaiki temuan dan tambahkan pemeriksaan yang belum tercakup bila perlu. | Susun dan coba panduan pencadangan/pemulihan database lokal dengan data percobaan; catat hasil serta batasannya. | Damiri |
| Damiri | Jalankan skenario API dan Postman; lengkapi matriks hasil aktual beserta langkah reproduksi. | Coba instalasi proyek dari clone pada lingkungan terpisah, lalu perbaiki panduan berdasarkan kendala yang benar-benar ditemukan. | Bilal |

Jika tidak ada masalah pada bagian yang diperiksa, sepakati peningkatan nyata sebelum membuat PR. Catatan verifikasi harus memuat perintah, hasil dan batasan sebenarnya, bukan hanya perubahan nama atau spasi.

## Cara bekerja setelah menerima undangan

1. Terima undangan GitHub menggunakan akun sendiri. Clone repository setelah unggahan awal tersedia; ikuti README untuk memasang proyek. Jangan mengunggah `.env`, token, database atau folder dependency.
2. Buat branch dari `main` terbaru, misalnya `docs/nofia-laporan`, `test/damiri-api`, atau `fix/viryal-pencarian`.
3. Kerjakan tugas, periksa hasilnya, lalu commit menggunakan identitas sendiri dan push branch tersebut.
4. Buka PR ke `main`. Tuliskan masalah, perubahan, cara memeriksa dan hasil sebenarnya. Cantumkan bantuan AI bila digunakan.
5. Minta rekan sesuai tabel meninjau perubahan melalui fitur review PR. Reviewer harus membaca perubahan dan memeriksa bagian yang relevan.
6. Gabungkan PR setelah review dan pemeriksaan yang relevan selesai. Simpan tautan PR yang telah digabung serta tautan review untuk laporan.

Unggahan langsung ke `main` saja tidak memenuhi persyaratan dua PR yang digabung. Dua PR dan satu review harus benar-benar tercatat pada akun setiap anggota.

Setiap orang memakai akun dan branch sendiri, membuka minimal dua PR, dan memberikan minimal satu review pada PR rekannya. Gabungkan PR hanya setelah pemeriksaan dan review selesai. Jangan membuat PR kosong, mengaku sebagai anggota lain, mengarang screenshot, atau memundurkan tanggal commit. Untuk tugas yang sudah dibuat bersama asisten, jelaskan bantuan yang dipakai sesuai aturan kampus. Pembagian PR perlu dilakukan pada perubahan nyata berikutnya; jangan merekayasa riwayat kode yang sudah selesai.

## Status akses dan bukti

- Repository GitHub dan remote lokal sudah dibuat.
- Username empat anggota lain masih diperlukan; belum ada undangan collaborator yang dikirim.
- Kode awal sudah diunggah dengan persetujuan Bilal melalui commit `a9c10ad`. `.env`, database lokal dan cadangan tidak ikut diunggah.
- GitHub Actions sudah dipicu oleh unggahan. Periksa hasil sebenarnya pada tab Actions repository.
- Belum ada PR, review, tag atau release yang dapat diklaim selesai.
- Persentase kontribusi dan paraf ditentukan kelompok berdasarkan pekerjaan sebenarnya.

