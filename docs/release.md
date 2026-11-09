# Checklist kandidat release v1.1.0

Versi kontrak: OpenAPI 3.0.3, `info.version: 1.1.0`. Route mayor: `/api/v1`. Berkas `VERSION` dan `CHANGELOG.md` sudah disiapkan. Kode sudah diunggah ke GitHub; tag dan release final belum diterbitkan.

## Sebelum push

Repository publik kelompok sudah dibuat: https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2. Remote `origin` lokal sudah terhubung. Commit awal `a9c10ad` sudah diunggah menggunakan akun Bilal setelah persetujuan publikasi. Workflow pertama: https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/actions/runs/42480301529. Periksa juga workflow pada commit terbaru melalui tab Actions.

- Jalankan kedua suite tes dan lint sesuai README. Baca hasil aktual di `hasil-pengujian.md`.
- Periksa `git status`, lalu pastikan `.env`, `.local`, database dan output tes yang memuat rahasia tidak ikut masuk.
- Tinjau identitas Nofia Lindawati (23215034) serta salinan laporan/PPT di folder induk `Hasil UTS Kelompok 2`.
- Review kode dan dokumentasi oleh anggota kelompok; jelaskan penggunaan bantuan alat sesuai ketentuan kampus.

## Memerlukan akun/akses GitHub anggota

1. Repository sudah dibuat pada akun Bilal dan remote sudah terhubung. Pada 9 November 2026, Damiri (`Ahmadtekor`) serta Viryal (`viryal280605`) sudah menjadi collaborator; undangan Septina (`AstiNabila`) dan Nofia (`nofialindawati99-coder`) masih menunggu penerimaan. Akses dosen perlu dikonfirmasi.
2. Atur nama/email Git milik sendiri pada komputer masing-masing. Lakukan commit setelah meninjau perubahan. Hubungkan repository memakai URL milik kelompok:

```powershell
git remote -v
git push -u origin main
```

Perintah push memerlukan commit dan login GitHub melalui Git Credential Manager. Remote proyek sudah terpasang; jangan menaruh token di URL.

3. Kerjakan perubahan nyata melalui branch dan PR sesuai `kontribusi.md`. Setiap anggota minimal dua PR yang digabung dan satu review pada PR rekan. Catat tautan PR dan review sebenarnya pada laporan. Jangan mengklaim bahwa inisialisasi lokal adalah PR atau review.
4. Pastikan workflow `.github/workflows/tests.yml` berjalan dan lulus di GitHub. Hasil lokal tidak sama dengan hasil Actions.
5. Setelah semua persyaratan dosen terpenuhi, maintainer memberi tag dan membuat release dari commit yang telah digabung:

```powershell
git tag -a v1.1.0 -m "Release v1.1.0 - Web Service Ketersediaan Buku"
git push origin v1.1.0
```

6. Pada GitHub Releases, pilih tag v1.1.0 dan gunakan changelog sebagai dasar catatan rilis. Lampirkan dokumentasi/screenshot yang sudah ditinjau. Pastikan arsip tidak menyertakan `.env`, `.local`, `vendor`, `node_modules` atau database lokal.

[Milestone UTS](https://github.com/bilaalmuhammad1030/uts-web-service-perpustakaan-kelompok-2/milestone/1) dan sepuluh issue pekerjaan sudah dibuat. Daftar akun, issue dan status akses ada di `kontribusi.md`. Draf release `v1.1.0` sudah disiapkan di GitHub Releases; tag dan release final belum dipublikasikan. Papan Projects masih perlu dibuat melalui antarmuka GitHub karena izin API Projects belum tersedia. PR/review nyata tiap anggota tetap wajib diselesaikan; pengunggahan awal serta issue tidak membuktikan persyaratan kontribusi telah terpenuhi.
