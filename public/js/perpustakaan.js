'use strict';
const $ = (selector) => document.querySelector(selector);
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const admin = document.body.dataset.portal === 'petugas';
let token = sessionStorage.getItem('petugas_token') || '';
let categories = [], page = 1, lastPage = 1, books = [], requestNumber = 0;
const time = value => value ? new Date(value).toLocaleString('id-ID', {timeZone:'Asia/Jakarta',dateStyle:'medium',timeStyle:'short'}) + ' WIB' : 'Belum dicatat';
function status(message, error = false) { $('#status').textContent = message; $('#status').classList.toggle('error',error); }
async function api(path, options = {}) {
    const response = await fetch('/api/v1/' + path, {...options, headers:{Accept:'application/json',...(options.body ? {'Content-Type':'application/json'} : {}),...(token ? {Authorization:'Bearer ' + token} : {}),...options.headers}});
    const body = response.status === 204 ? null : await response.json();
    if (!response.ok) {
        if (response.status === 401 && admin) { token = ''; sessionStorage.removeItem('petugas_token'); showAuth(); }
        const detail = Object.values(body?.error?.details || {}).flat().join(' ');
        throw new Error((body?.error?.message || 'Permintaan gagal.') + (detail ? ' ' + detail : ''));
    }
    return body;
}
function modal(title, html) { $('#modal-title').textContent = title; $('#modal-content').innerHTML = html; $('#modal-error').textContent = ''; if (!$('#modal').open) $('#modal').showModal(); }
$('#close-modal').onclick = () => $('#modal').close();
$('#modal').addEventListener('close', () => { $('#modal-content').replaceChildren(); $('#modal-error').textContent = ''; });
function guard(action, dialog = false) { return async event => { event?.preventDefault(); const button = event?.submitter; if (button) button.disabled = true; try { await action(event); } catch(e) { if (dialog && $('#modal').open) $('#modal-error').textContent = e.message; else status(e.message, true); } finally { if (button) button.disabled = false; } }; }
async function loadCategories() {
    categories = (await api('kategori')).data;
    const select = $('#category-filter');
    if (select) select.innerHTML = '<option value="">Semua kategori</option>' + categoryOptions();
}
function categoryOptions(selected = '') { return categories.map(c => `<option value="${c.id}" ${String(c.id) === String(selected) ? 'selected' : ''}>${escapeHtml(c.nama)}</option>`).join(''); }
function bookCover(book) {
    const themes = ['theme-blue', 'theme-sand', 'theme-clay', 'theme-olive', 'theme-plum', 'theme-forest'];
    const colorKey = [...book.kode_buku].reduce((sum, character) => sum + character.charCodeAt(0), 0);
    const theme = themes[colorKey % themes.length];
    return `<div class="book-cover ${theme}" aria-hidden="true"><span class="cover-kicker">${escapeHtml(book.kategori.nama).toUpperCase()}</span><span class="cover-title">${escapeHtml(book.judul)}</span><span class="cover-author">${escapeHtml(book.penulis)}</span><span class="cover-footer">RUANG BACA</span></div>`;
}
function renderFeaturedBook() {
    const featured = $('#featured-book');
    if (!featured || featured.dataset.ready || !books.length) return;
    const book = books.find(item => item.kategori.nama.toLowerCase() === 'novel') || books[0];
    featured.innerHTML = `<span class="featured-label">DI ANTARA KOLEKSI KAMI</span><div class="featured-stage">${bookCover(book)}</div><div class="featured-info"><span class="eyebrow">${escapeHtml(book.kategori.nama)}</span><h2>${escapeHtml(book.judul)}</h2><p class="featured-author">${escapeHtml(book.penulis)}</p><span class="stock-tag ${book.jumlah_tersedia ? '' : 'empty'}">${book.jumlah_tersedia ? book.jumlah_tersedia + ' tersedia' : 'Stok kosong'}</span><span class="featured-rack">Temukan di rak ${escapeHtml(book.lokasi_rak)}</span><button class="featured-action" data-detail="${book.id}">Kenali buku ini <span aria-hidden="true">↗</span></button></div>`;
    featured.dataset.ready = 'true';
}
function renderStudentBooks(result) {
    $('#collection-count').textContent = `${result.meta.total} buku ditemukan`;
    renderFeaturedBook();
    $('#book-grid').innerHTML = books.length ? books.map(book => `<article class="book-card"><div class="book-visual">${bookCover(book)}</div><div class="book-info"><div class="card-top"><span class="category-tag">${escapeHtml(book.kategori.nama)}</span></div><h3>${escapeHtml(book.judul)}</h3><p class="author">${escapeHtml(book.penulis)}</p><span class="book-code">${escapeHtml(book.kode_buku)}</span></div><div class="card-bottom"><span class="stock-tag ${book.jumlah_tersedia ? '' : 'empty'}">${book.jumlah_tersedia ? book.jumlah_tersedia + ' tersedia' : 'Stok kosong'}</span><span class="rack"><svg class="icon" aria-hidden="true"><use href="#icon-rack"/></svg>Rak ${escapeHtml(book.lokasi_rak)}</span></div><button class="detail-button" data-detail="${book.id}">Lihat detail buku <svg class="icon" aria-hidden="true"><use href="#icon-arrow"/></svg></button></article>`).join('') : '<p class="empty-state">Buku belum ditemukan. Coba judul, penulis, atau kategori lain.</p>';
    document.querySelectorAll('[data-detail]').forEach(button => button.onclick = guard(() => detail(button.dataset.detail)));
}

async function loadBooks() {
    const currentRequest = ++requestNumber;
    status('Memuat koleksi…');
    const form = admin ? $('#admin-search') : $('#search-form');
    const params = new URLSearchParams(new FormData(form));
    for (const [key,value] of [...params]) if (value === '') params.delete(key);
    params.set('page', page); params.set('per_page', admin ? 10 : 6);
    if (params.get('sort') === 'stok_diperbarui_at') params.set('direction','desc');
    const result = await api('buku?' + params);
    if (currentRequest !== requestNumber) return;
    if (page > result.meta.last_page && page > 1) { page = result.meta.last_page; return loadBooks(); }
    books = result.data; lastPage = result.meta.last_page;
    status('');
    $('#page-info').textContent = `Halaman ${result.meta.current_page} dari ${lastPage}`;
    $('#prev').disabled = page <= 1; $('#next').disabled = page >= lastPage;
    if (admin) renderAdmin(); else renderStudentBooks(result);
}
$('#prev').onclick = guard(async () => { if (page > 1) {page--; await loadBooks();} });
$('#next').onclick = guard(async () => { if (page < lastPage) {page++; await loadBooks();} });
async function detail(id) {
    const b = (await api('buku/' + id)).data;
    modal(b.judul, `<p class="muted">${escapeHtml(b.penulis)} · ${escapeHtml(b.kode_buku)}</p><dl class="detail-meta"><div><dt>Kategori</dt><dd>${escapeHtml(b.kategori.nama)}</dd></div><div><dt>Lokasi rak</dt><dd>${escapeHtml(b.lokasi_rak)}</dd></div><div><dt>Ketersediaan</dt><dd>${b.jumlah_tersedia} dari ${b.jumlah_total} eksemplar</dd></div><div><dt>Pembaruan stok</dt><dd>${time(b.stok_diperbarui_at)}</dd></div><div><dt>Penerbit</dt><dd>${escapeHtml(b.penerbit || "Belum dicatat")}</dd></div><div><dt>Tahun terbit</dt><dd>${escapeHtml(b.tahun_terbit || "Belum dicatat")}</dd></div><div><dt>ISBN</dt><dd>${escapeHtml(b.isbn || 'Belum dicatat')}</dd></div></dl><p class="detail-description">${escapeHtml(b.deskripsi || 'Deskripsi belum tersedia.')}</p><button id="metadata-btn" class="button secondary" style="margin-top:22px">Cari metadata di Open Library ↗</button><div id="metadata-results" aria-live="polite"></div>`);
    $('#metadata-btn').onclick = guard(async () => {
        $('#metadata-results').textContent = 'Mengambil metadata tambahan…';
        const data = (await api('metadata?q=' + encodeURIComponent(b.judul))).data;
        $('#metadata-results').innerHTML = `<p class="muted" style="margin-top:16px">${escapeHtml(data.message)}${data.cached ? ' (Dari cache)' : ''}</p>` + data.items.map(item => `<div class="metadata-item"><strong>${escapeHtml(item.judul)}</strong><p>${escapeHtml(item.penulis)} · ${escapeHtml(item.tahun || 'Tahun tidak diketahui')}<br>ISBN: ${escapeHtml(item.isbn || 'Tidak tersedia')}</p></div>`).join('') + (!data.fallback && !data.items.length ? '<p class="muted">Metadata tidak ditemukan.</p>' : '');
    },true);
}
function showAuth() { $('#login-panel').hidden = !!token; $('#admin-panel').hidden = !token; $('#greeting').textContent = 'Selamat bekerja, ' + (sessionStorage.getItem('petugas_nama') || 'Petugas') + '.'; }
function renderAdmin() {
    $('#admin-books').innerHTML = books.length ? books.map(b => `<tr><td><strong>${escapeHtml(b.judul)}</strong><small>${escapeHtml(b.kode_buku)} · ${escapeHtml(b.penulis)}</small></td><td>${escapeHtml(b.penerbit || "Belum dicatat")}<small>${escapeHtml(b.tahun_terbit || "Tahun belum dicatat")}</small></td><td>${escapeHtml(b.kategori.nama)}</td><td>${escapeHtml(b.lokasi_rak)}</td><td><strong>${b.jumlah_tersedia}</strong> / ${b.jumlah_total}</td><td><div class="row-actions"><button data-action="edit" data-id="${b.id}">Edit</button><button data-action="stock" data-id="${b.id}">Stok</button><button data-action="history" data-id="${b.id}">Riwayat</button><button data-action="delete" data-id="${b.id}" class="danger">Hapus</button></div></td></tr>`).join('') : '<tr><td colspan="6">Buku tidak ditemukan.</td></tr>';
    document.querySelectorAll('[data-action]').forEach(button => button.onclick = guard(async () => {
        const id = button.dataset.id;
        if (button.dataset.action === 'edit') await bookForm(id);
        if (button.dataset.action === 'stock') await stockForm(id);
        if (button.dataset.action === 'history') await history(id);
        if (button.dataset.action === 'delete' && confirm('Arsipkan buku ini dari katalog? Riwayat stok tetap disimpan.')) { await api('buku/' + id,{method:'DELETE'}); await loadBooks(); status('Buku berhasil diarsipkan.'); }
    }));
}
async function bookForm(id = null) {
    await loadCategories();
    if (!categories.length) throw new Error('Tambahkan kategori terlebih dahulu melalui Kelola kategori.');
    const b = id ? (await api('buku/' + id)).data : {};
    const field = (name,label,value = '',type = 'text',extra = '') => `<label>${label}<input name="${name}" type="${type}" value="${escapeHtml(value)}" ${extra}></label>`;
    modal(id ? 'Edit data buku' : 'Tambah buku', `<form id="book-form" class="dialog-form"><div class="form-row">${field('kode_buku','Kode buku',b.kode_buku,'text','required maxlength="20" pattern="[A-Za-z0-9-]+"')}${field('isbn','ISBN (opsional)',b.isbn,'text','maxlength="13"')}</div>${field('judul','Judul buku',b.judul,'text','required maxlength="150"')}${field('penulis','Penulis',b.penulis,'text','required maxlength="100"')}<div class="form-row">${field('penerbit','Penerbit',b.penerbit,'text','required maxlength="100"')}${field('tahun_terbit','Tahun terbit',b.tahun_terbit,'number','required min="1901" max="' + new Date().getFullYear() + '"')}</div><div class="form-row"><label>Kategori<select name="kategori_id" required>${categoryOptions(b.kategori?.id)}</select></label>${field('lokasi_rak','Lokasi rak',b.lokasi_rak,'text','required maxlength="50"')}</div><label>Deskripsi<textarea name="deskripsi" maxlength="5000">${escapeHtml(b.deskripsi)}</textarea></label>${id ? '<p class="muted">Perbarui jumlah buku melalui tombol Stok.</p>' : `<div class="form-row">${field('jumlah_total','Jumlah total',0,'number','required min="0" max="1000000"')}${field('jumlah_tersedia','Jumlah tersedia',0,'number','required min="0" max="1000000"')}</div>`}<button class="button">Simpan buku</button></form>`);
    $('#book-form').onsubmit = guard(async event => { await api('buku' + (id ? '/' + id : ''), {method:id ? 'PUT' : 'POST',body:JSON.stringify(Object.fromEntries(new FormData(event.target)))}); $('#modal').close(); await loadBooks(); status('Data buku berhasil disimpan.'); },true);
}
async function stockForm(id) {
    const b = (await api('buku/' + id)).data;
    modal('Perbarui stok', `<p>${escapeHtml(b.judul)}</p><p class="muted">Versi stok ${b.versi_stok} · ${time(b.stok_diperbarui_at)}</p><form id="stock-form" class="dialog-form" style="margin-top:20px"><div class="form-row"><label>Jumlah total<input name="jumlah_total" type="number" min="0" max="1000000" value="${b.jumlah_total}" required></label><label>Jumlah tersedia<input name="jumlah_tersedia" type="number" min="0" max="1000000" value="${b.jumlah_tersedia}" required></label></div><label>Alasan pembaruan<textarea name="alasan" required minlength="3" maxlength="255" placeholder="Contoh: pengecekan koleksi di rak"></textarea></label><button class="button">Simpan perubahan stok</button><p class="muted">Jika stok telah diubah petugas lain, tutup dialog dan buka kembali untuk mengambil versi terbaru.</p></form>`);
    $('#stock-form').onsubmit = guard(async event => { const data = Object.fromEntries(new FormData(event.target)); data.versi_stok = b.versi_stok; await api(`buku/${id}/stok`,{method:'PATCH',body:JSON.stringify(data)}); $('#modal').close(); await loadBooks(); status('Stok dan riwayat berhasil disimpan.'); },true);
}
async function history(id, historyPage = 1) {
    const result = await api(`buku/${id}/riwayat-stok?page=${historyPage}`);
    modal('Riwayat stok', result.data.map(h => `<div class="history-item"><strong>${h.tersedia_sebelum} → ${h.tersedia_sesudah} tersedia</strong> · total ${h.total_sebelum} → ${h.total_sesudah}<p>${escapeHtml(h.alasan)}</p><small>${escapeHtml(h.petugas.nama)} · versi ${h.versi_stok} · ${time(h.created_at)}</small></div>`).join('') || '<p>Belum ada riwayat.</p>');
    $('#modal-content').insertAdjacentHTML('beforeend', `<div class="pagination"><button class="button secondary" id="history-prev" ${historyPage <= 1 ? 'disabled' : ''}>Sebelumnya</button><span>${historyPage} / ${result.meta.last_page}</span><button class="button secondary" id="history-next" ${historyPage >= result.meta.last_page ? 'disabled' : ''}>Berikutnya</button></div>`);
    $('#history-prev').onclick = guard(() => history(id,historyPage-1),true); $('#history-next').onclick = guard(() => history(id,historyPage+1),true);
}
async function categoryForm() {
    await loadCategories();
    modal('Kelola kategori', '<form id="category-form" class="dialog-form"><label>Nama kategori<input name="nama" required maxlength="100" placeholder="Contoh: Sejarah"></label><button class="button">Tambah kategori</button></form><div class="category-list">' + categories.map(c => `<div class="category-item"><span>${escapeHtml(c.nama)}</span><button class="button quiet" data-cat-edit="${c.id}">Edit</button><button class="button quiet" data-cat-delete="${c.id}">Hapus</button></div>`).join('') + '</div>');
    $('#category-form').onsubmit = guard(async event => { await api('kategori',{method:'POST',body:JSON.stringify(Object.fromEntries(new FormData(event.target)))}); await categoryForm(); status('Kategori berhasil ditambahkan.'); },true);
    document.querySelectorAll('[data-cat-delete]').forEach(button => button.onclick = guard(async () => { if (!confirm('Hapus kategori ini? Kategori yang masih digunakan buku tidak dapat dihapus.')) return; await api('kategori/' + button.dataset.catDelete,{method:'DELETE'}); await categoryForm(); },true));
    document.querySelectorAll('[data-cat-edit]').forEach(button => button.onclick = guard(async () => {
        const c = categories.find(item => String(item.id) === button.dataset.catEdit);
        modal('Edit kategori', `<form id="edit-category" class="dialog-form"><label>Nama kategori<input name="nama" value="${escapeHtml(c.nama)}" maxlength="100" required></label><button class="button">Simpan kategori</button></form>`);
        $('#edit-category').onsubmit = guard(async event => { await api('kategori/' + c.id,{method:'PUT',body:JSON.stringify(Object.fromEntries(new FormData(event.target)))}); await categoryForm(); await loadBooks(); },true);
    },true));
}
if (admin) {
    $('#password-toggle').onclick = () => {
        const input = $('#login-password');
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        $('#password-toggle').textContent = visible ? 'Sembunyikan' : 'Lihat';
        $('#password-toggle').setAttribute('aria-pressed', String(visible));
        $('#password-toggle').setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    };
    showAuth();
    $('#login-form').onsubmit = guard(async event => {
        const data = (await api('auth/login',{method:'POST',body:JSON.stringify(Object.fromEntries(new FormData(event.target)))})).data;
        token = data.token; sessionStorage.setItem('petugas_token',token); sessionStorage.setItem('petugas_nama',data.petugas.nama); event.target.reset(); showAuth(); await loadBooks();
    });
    $('#logout').onclick = guard(async () => { await api('auth/logout',{method:'POST'}); token = ''; sessionStorage.removeItem('petugas_token'); sessionStorage.removeItem('petugas_nama'); showAuth(); status('Anda sudah keluar.'); });
    $('#add-book').onclick = guard(() => bookForm()); $('#categories-btn').onclick = guard(categoryForm);
    $('#admin-search').onsubmit = guard(async () => {page = 1; await loadBooks();});
    if (token) guard(loadBooks)();
} else {
    $('#search-form').onsubmit = guard(async () => {page = 1; await loadBooks();});
    $('#search-form').addEventListener('change', guard(async event => {
        if (event.target.tagName !== 'SELECT') return;
        page = 1;
        await loadBooks();
    }));
    $('#search-form').onreset = () => setTimeout(guard(async () => {page=1;await loadBooks();}),0);
    guard(async () => {await loadCategories(); await loadBooks();})();
}
