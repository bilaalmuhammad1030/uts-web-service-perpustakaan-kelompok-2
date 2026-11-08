<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Temukan buku dan periksa ketersediaan koleksi perpustakaan sekolah.">
    <title>{{ $portal === 'petugas' ? 'Ruang Petugas' : 'Katalog Buku' }} · Perpustakaan Kelompok 2</title>
    <link rel="stylesheet" href="{{ asset('css/perpustakaan.css') }}">
    <script src="{{ asset('js/perpustakaan.js') }}" defer></script>
</head>
<body data-portal="{{ $portal }}">
<header class="header">
    <a class="brand" href="{{ route('katalog') }}"><span class="brand-icon" aria-hidden="true">▤</span><span>Ruang Baca<small>PERPUSTAKAAN SEKOLAH</small></span></a>
    <nav aria-label="Navigasi utama"><a href="{{ route('katalog') }}" @class(['active' => $portal === 'siswa'])>Katalog buku</a><a href="{{ route('petugas') }}" @class(['active' => $portal === 'petugas'])>Ruang petugas <span aria-hidden="true">↗</span></a></nav>
</header>
<main>
@if ($portal === 'siswa')
    <section class="hero">
        <div><span class="eyebrow">SEBUAH RUANG UNTUK BERTUMBUH</span><h1>Buku berikutnya,<br><em>awal cerita baru.</em></h1><p>Temukan bacaanmu, periksa ketersediaannya,<br class="desktop"> lalu kunjungi rak perpustakaan.</p><a class="text-link" href="#koleksi">Jelajahi koleksi <span aria-hidden="true">↓</span></a></div>
        <div class="shelf-art" aria-hidden="true"><span class="art-note">Buka buku.<br>Buka kemungkinan.</span><div class="spines"><i>ILMU & KEHIDUPAN</i><i>CERITA DARI NUSANTARA</i><i>JENDELA DUNIA</i><i>RASA INGIN TAHU</i></div><span class="art-caption">SELALU ADA HAL BARU UNTUK DITEMUKAN</span></div>
    </section>
    <section id="koleksi" class="collection">
        <div class="section-heading"><div><span class="eyebrow">RAK DIGITAL KITA</span><h2>Jelajahi koleksi</h2></div><span id="collection-count" class="muted">Memuat koleksi…</span></div>
        <form id="search-form" class="filters">
            <label class="search-field"><span class="sr-only">Cari judul atau penulis</span><span aria-hidden="true">⌕</span><input name="q" placeholder="Cari judul buku atau nama penulis…" maxlength="200"><button type="submit">Cari buku</button></label>
            <div class="filter-row"><label>Kategori<select name="kategori_id" id="category-filter"><option value="">Semua kategori</option></select></label><label>Ketersediaan<select name="tersedia"><option value="">Semua buku</option><option value="1">Tersedia di rak</option><option value="0">Stok kosong</option></select></label><label>Urutkan<select name="sort"><option value="judul">Judul A–Z</option><option value="penulis">Nama penulis</option><option value="stok_diperbarui_at">Pembaruan stok</option></select></label><button type="reset" class="button quiet">Atur ulang</button></div>
        </form>
        <p id="status" class="status" role="status" aria-live="polite"></p>
        <div id="book-grid" class="book-grid" aria-live="polite"></div>
        <div class="pagination"><button id="prev" class="button secondary">← Sebelumnya</button><span id="page-info"></span><button id="next" class="button secondary">Berikutnya →</button></div>
    </section>
    <aside class="info-banner"><span class="info-icon" aria-hidden="true">i</span><div><strong>Sudah menemukan buku yang dicari?</strong><p>Catat lokasi rak dan kunjungi perpustakaan. Informasi di sini menunjukkan ketersediaan terakhir yang dicatat petugas.</p></div></aside>
@else
    <section class="admin-heading"><span class="eyebrow">KELOLA KOLEKSI DENGAN MUDAH</span><h1>Ruang petugas</h1><p class="muted">Data buku yang rapi membantu siswa menemukan bacaan mereka.</p></section>
    <p id="status" class="status" role="status" aria-live="polite"></p>
    <section id="login-panel" class="login-panel"><div><span class="eyebrow">AKSES PETUGAS</span><h2>Selamat datang kembali.</h2><p>Masuk untuk mengelola buku, kategori,<br>dan memperbarui ketersediaan koleksi.</p></div><form id="login-form"><label>Email<input name="email" type="email" autocomplete="username" required placeholder="Email petugas"></label><label>Kata sandi<input name="password" type="password" autocomplete="current-password" required></label><button class="button" type="submit">Masuk ke ruang petugas →</button><small class="muted">Gunakan akun petugas yang disiapkan saat instalasi.</small></form></section>
    <section id="admin-panel" hidden>
        <div class="section-heading"><div><h2>Kelola koleksi</h2><p id="greeting" class="muted"></p></div><div class="actions"><button class="button secondary" id="categories-btn">Kelola kategori</button><button class="button" id="add-book">+ Tambah buku</button><button class="button quiet" id="logout">Keluar</button></div></div>
        <form id="admin-search" class="admin-search"><label><span class="sr-only">Cari buku</span><input name="q" placeholder="Cari judul atau penulis…" maxlength="200"></label><button class="button secondary">Cari</button></form>
        <div class="table-wrap"><table><caption class="sr-only">Daftar koleksi perpustakaan</caption><thead><tr><th>Buku / penulis</th><th>Penerbit / tahun</th><th>Kategori</th><th>Rak</th><th>Tersedia / total</th><th>Tindakan</th></tr></thead><tbody id="admin-books"></tbody></table></div>
        <div class="pagination"><button id="prev" class="button secondary">← Sebelumnya</button><span id="page-info"></span><button id="next" class="button secondary">Berikutnya →</button></div>
    </section>
@endif
</main>
<footer><a class="footer-brand" href="{{ route('katalog') }}">Ruang Baca <span>·</span> Kelompok 2</a><p>Web Service Ketersediaan Buku Perpustakaan</p><small>UTS Pemrograman Web Service · v1.1.0<br>Mochamad Bilal Muadzin · 23215025 &nbsp; / &nbsp; Nofia</small></footer>
<dialog id="modal" aria-labelledby="modal-title"><div class="modal-heading"><h2 id="modal-title"></h2><button id="close-modal" class="icon-button" aria-label="Tutup dialog">×</button></div><p id="modal-error" class="status error" role="alert"></p><div id="modal-content"></div></dialog>
</body>
</html>
