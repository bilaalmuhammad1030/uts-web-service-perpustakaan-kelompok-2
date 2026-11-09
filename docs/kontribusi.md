# Rencana kontribusi nyata Kelompok 2

Anggota: Damiri (23215020), Mochamad Bilal Muadzin (23215025), Nofia Lindawati (23215034), Viryal Arliyani (23215033), dan Septina Asti Nabila (23215005).

Repository: https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2.

Bilal adalah pemilik repository dengan akun `bilaalmuhammad1030`. Pemeriksaan akses pada 9 November 2026: Damiri (`Ahmadtekor`) dan Viryal (`viryal280605`) sudah menjadi collaborator. Undangan Septina (`AstiNabila`) serta Nofia (`nofialindawati99-coder`) masih menunggu penerimaan dari akun masing-masing. Akses menulis memungkinkan anggota mengunggah branch dan membuat PR; akses ini belum menjadi bukti kontribusi.

Dokumen ini memuat rencana pekerjaan dan status persiapan GitHub. Issue dan draf release belum membuktikan persyaratan PR yang digabung serta review tiap anggota telah terpenuhi.

## Pekerjaan yang sudah dicatat di GitHub

[Milestone UTS - M3 Core API v1.1.0](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/milestone/1) memuat sepuluh issue berikut. Issue adalah rencana; kontribusi baru tercatat setelah pekerjaan dilakukan, PR ditinjau dan digabung.

| Anggota / akun | Issue pertama | Issue kedua |
| --- | --- | --- |
| Bilal / bilaalmuhammad1030 | [#1 Panduan demo tunnel](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/1) | [#2 Perlindungan akun lama saat seeding](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/2) |
| Nofia / nofialindawati99-coder | [#3 Laporan final](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/3) | [#4 PPT dan pembicara](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/4) |
| Viryal / viryal280605 | [#5 Akses keyboard](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/5) | [#6 Portal pada ponsel](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/6) |
| Septina / AstiNabila | [#7 Constraint dan data awal](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/7) | [#8 Backup dan restore](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/8) |
| Damiri / Ahmadtekor | [#9 Postman](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/9) | [#10 Instalasi dari clone](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/issues/10) |

Issue untuk Nofia dan Septina belum diberi assignee karena undangan masih tertunda; pemilik repository dapat menambahkan assignee setelah undangan diterima. Pembagian ini memperinci usulan di bawah, dengan dua tugas Bilal berdasarkan kekurangan yang ditemukan setelah demo online dan perubahan email.

Papan GitHub Projects belum dibuat karena akses API yang tersedia tidak memiliki izin Projects. Pemilik repository dapat membuka tab **Projects → New project → Board**, memberi nama **UTS Kelompok 2 - M3**, lalu menambahkan sepuluh issue dari milestone ke kolom Todo, In Progress, dan Done. Pindahkan issue ke Done setelah PR terkait digabung dan bukti pemeriksaan tersedia.

Draf release `v1.1.0` sudah disiapkan di tab Releases untuk ditinjau pemilik repository. Draf belum merupakan release publik dan belum menggantikan persyaratan tag final. Setelah PR wajib selesai dan Actions lulus pada commit final, tinjau target release, lengkapi catatan dan publikasikan sebelum batas pengumpulan.

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
- Username empat anggota sudah diketahui. Damiri dan Viryal sudah menjadi collaborator; Septina dan Nofia masih perlu menerima undangan. Akses dosen belum dikonfirmasi.
- Kode awal sudah diunggah dengan persetujuan Bilal melalui commit `a9c10ad`. `.env`, database lokal dan cadangan tidak ikut diunggah.
- GitHub Actions sudah dipicu oleh unggahan. Periksa hasil sebenarnya pada tab Actions repository.
- Sepuluh issue dan milestone UTS sudah dibuat. Draf release disiapkan; tag/release final, PR anggota dan review masih perlu diselesaikan.
- Persentase kontribusi dan paraf ditentukan kelompok berdasarkan pekerjaan sebenarnya.
