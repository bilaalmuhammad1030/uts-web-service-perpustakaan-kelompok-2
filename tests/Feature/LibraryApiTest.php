<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Petugas;
use App\Models\RiwayatStok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class LibraryApiTest extends TestCase
{
    use RefreshDatabase;

    private function petugas(): Petugas
    {
        $petugas = Petugas::factory()->create(['token_hash' => hash('sha256', 'test-token'), 'token_expires_at' => now()->addHour()]);
        $this->withToken('test-token');

        return $petugas;
    }

    private function payload(): array
    {
        return ['kode_buku' => 'UTS-001', 'kategori_id' => Kategori::factory()->create()->id,
            'judul' => 'Buku Uji', 'penulis' => 'Bilal', 'lokasi_rak' => 'A-01',
            'penerbit' => 'Penerbit Uji', 'tahun_terbit' => 2020,
            'jumlah_total' => 10, 'jumlah_tersedia' => 8];
    }

    public function test_daftar_filter_pencarian_pengurutan_dan_pagination(): void
    {
        $kategori = Kategori::factory()->create();
        Buku::factory()->create(['judul' => 'Belajar Web', 'penulis' => 'Bilal', 'kategori_id' => $kategori->id]);
        Buku::factory()->create(['judul' => 'API Web', 'penulis' => 'Bilal', 'kategori_id' => $kategori->id]);
        Buku::factory()->create(['judul' => 'Kosong', 'penulis' => 'Bilal', 'kategori_id' => $kategori->id, 'jumlah_tersedia' => 0]);
        Buku::factory()->create(['penulis' => 'Nama Lain']);
        $url = '/api/v1/buku?q=Bilal&tersedia=1&sort=judul&per_page=1&kategori_id='.$kategori->id;
        $this->getJson($url)->assertOk()->assertJsonPath('meta.total', 2)->assertJsonPath('data.0.judul', 'API Web')->assertJsonCount(1, 'data');
        $this->getJson($url.'&page=2')->assertOk()->assertJsonPath('data.0.judul', 'Belajar Web');
    }

    public function test_detail_buku_mencakup_rak_kategori_stok_dan_waktu(): void
    {
        $buku = Buku::factory()->create();
        $this->getJson('/api/v1/buku/'.$buku->id)->assertOk()
            ->assertJsonPath('data.lokasi_rak', 'A-01')
            ->assertJsonStructure(['data' => ['kategori' => ['id', 'nama'], 'jumlah_total', 'jumlah_tersedia', 'stok_diperbarui_at', 'versi_stok']]);
    }

    public function test_pembuatan_buku_201_location_dan_riwayat_awal(): void
    {
        $this->petugas();
        $response = $this->postJson('/api/v1/buku', $this->payload())->assertCreated();
        $id = $response->json('data.id');
        $response->assertHeader('Location', url('/api/v1/buku/'.$id));
        $this->assertDatabaseHas('riwayat_stok', ['buku_id' => $id, 'versi_stok' => 1, 'tersedia_sesudah' => 8]);
    }

    public function test_stok_di_atas_total_dan_negatif_menghasilkan_422(): void
    {
        $this->petugas();
        $payload = $this->payload();
        $this->postJson('/api/v1/buku', [...$payload, 'jumlah_tersedia' => 11])->assertUnprocessable()->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->postJson('/api/v1/buku', [...$payload, 'jumlah_tersedia' => -1])->assertUnprocessable();
        $this->assertDatabaseCount('buku', 0);
    }

    public function test_kode_buku_harus_unik_dan_kategori_harus_ada(): void
    {
        $this->petugas();
        $buku = Buku::factory()->create();
        $this->postJson('/api/v1/buku', [...$this->payload(), 'kode_buku' => $buku->kode_buku])->assertUnprocessable();
        $this->postJson('/api/v1/buku', [...$this->payload(), 'kategori_id' => 999999])->assertUnprocessable();
    }

    public function test_buku_dan_endpoint_tidak_ditemukan_404_json_seragam(): void
    {
        foreach (['/api/v1/buku/99999', '/api/v1/tidak-ada'] as $url) {
            $this->getJson($url)->assertNotFound()->assertJsonPath('error.code', 'NOT_FOUND')->assertJsonStructure(['error' => ['code', 'message', 'details']]);
        }
    }

    public function test_pengelolaan_memerlukan_token_petugas(): void
    {
        $this->postJson('/api/v1/buku', $this->payload())->assertUnauthorized()->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_login_logout_dan_token_kedaluwarsa(): void
    {
        $petugas = Petugas::factory()->create();
        $this->postJson('/api/v1/auth/login', ['email' => $petugas->email, 'password' => 'salah'])->assertUnauthorized();
        $token = $this->postJson('/api/v1/auth/login', ['email' => $petugas->email, 'password' => 'KhususTes123!'])->assertOk()->json('data.token');
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();
        $this->withToken($token)->postJson('/api/v1/kategori', ['nama' => 'Ditolak'])->assertUnauthorized();
        $petugas->update(['token_hash' => hash('sha256', 'expired'), 'token_expires_at' => now()->subMinute()]);
        $this->withToken('expired')->postJson('/api/v1/kategori', ['nama' => 'Ditolak'])->assertUnauthorized();
    }

    public function test_perubahan_stok_dan_riwayat_disimpan_bersama(): void
    {
        $petugas = $this->petugas();
        $buku = Buku::factory()->create();
        $this->patchJson('/api/v1/buku/'.$buku->id.'/stok', [
            'jumlah_total' => 10, 'jumlah_tersedia' => 4, 'versi_stok' => 1, 'alasan' => 'Pengecekan rak',
        ])->assertOk()->assertJsonPath('data.versi_stok', 2)->assertJsonPath('data.jumlah_tersedia', 4);
        $this->assertDatabaseHas('riwayat_stok', ['buku_id' => $buku->id, 'petugas_id' => $petugas->id,
            'tersedia_sebelum' => 7, 'tersedia_sesudah' => 4, 'versi_stok' => 2]);
        $this->getJson('/api/v1/buku/'.$buku->id.'/riwayat-stok')->assertOk()->assertJsonPath('data.0.alasan', 'Pengecekan rak');
    }

    public function test_versi_lama_menghasilkan_409_tanpa_mengubah_data(): void
    {
        $this->petugas();
        $buku = Buku::factory()->create(['versi_stok' => 2]);
        $this->patchJson('/api/v1/buku/'.$buku->id.'/stok', [
            'jumlah_total' => 10, 'jumlah_tersedia' => 2, 'versi_stok' => 1, 'alasan' => 'Data lama',
        ])->assertConflict()->assertJsonPath('error.code', 'BUSINESS_CONFLICT');
        $this->assertDatabaseHas('buku', ['id' => $buku->id, 'jumlah_tersedia' => 7, 'versi_stok' => 2]);
        $this->assertDatabaseCount('riwayat_stok', 0);
    }

    public function test_validasi_stok_422_berbeda_dari_konflik_409(): void
    {
        $this->petugas();
        $buku = Buku::factory()->create();
        $this->patchJson('/api/v1/buku/'.$buku->id.'/stok', [
            'jumlah_total' => 2, 'jumlah_tersedia' => 3, 'versi_stok' => 1, 'alasan' => 'Tes validasi',
        ])->assertUnprocessable()->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->assertDatabaseCount('riwayat_stok', 0);
    }

    public function test_transaksi_rollback_jika_pencatatan_riwayat_gagal(): void
    {
        $this->petugas();
        $buku = Buku::factory()->create();
        RiwayatStok::creating(function (): void {
            throw new \RuntimeException('Simulasi gagal simpan audit');
        });
        try {
            $this->patchJson('/api/v1/buku/'.$buku->id.'/stok', [
                'jumlah_total' => 12, 'jumlah_tersedia' => 9, 'versi_stok' => 1, 'alasan' => 'Uji rollback',
            ])->assertStatus(500)->assertJsonPath('error.message', 'Terjadi gangguan pada server.');
            $this->assertDatabaseHas('buku', ['id' => $buku->id, 'jumlah_total' => 10, 'jumlah_tersedia' => 7, 'versi_stok' => 1]);
            $this->assertDatabaseCount('riwayat_stok', 0);
        } finally {
            RiwayatStok::flushEventListeners();
        }
    }

    public function test_pembuatan_buku_juga_rollback_jika_audit_gagal(): void
    {
        $this->petugas();
        RiwayatStok::creating(function (): void {
            throw new \RuntimeException('Simulasi gagal audit awal');
        });
        try {
            $this->postJson('/api/v1/buku', $this->payload())->assertStatus(500);
            $this->assertDatabaseCount('buku', 0);
        } finally {
            RiwayatStok::flushEventListeners();
        }
    }

    public function test_crud_kategori_dan_konflik_kategori_digunakan(): void
    {
        $this->petugas();
        $response = $this->postJson('/api/v1/kategori', ['nama' => 'Sejarah'])->assertCreated();
        $id = $response->json('data.id');
        $response->assertHeader('Location', url('/api/v1/kategori/'.$id));
        $this->putJson('/api/v1/kategori/'.$id, ['nama' => 'Sejarah Dunia'])->assertOk()->assertJsonPath('data.nama', 'Sejarah Dunia');
        Buku::factory()->create(['kategori_id' => $id]);
        $this->deleteJson('/api/v1/kategori/'.$id)->assertConflict();
        $empty = Kategori::factory()->create();
        $this->deleteJson('/api/v1/kategori/'.$empty->id)->assertNoContent();
    }

    public function test_edit_buku_tidak_boleh_melewati_endpoint_stok(): void
    {
        $this->petugas();
        $buku = Buku::factory()->create();
        $data = ['kode_buku' => $buku->kode_buku, 'kategori_id' => $buku->kategori_id, 'judul' => 'Judul Baru',
            'penulis' => 'Penulis Baru', 'lokasi_rak' => 'Z-01'];
        $data += ['penerbit' => 'Penerbit Baru', 'tahun_terbit' => 2021];
        $this->putJson('/api/v1/buku/'.$buku->id, [...$data, 'jumlah_total' => 30])->assertUnprocessable();
        $this->putJson('/api/v1/buku/'.$buku->id, $data)->assertOk()->assertJsonPath('data.judul', 'Judul Baru')->assertJsonPath('data.versi_stok', 1);
    }

    public function test_hapus_buku_mengarsipkan_dan_mempertahankan_audit(): void
    {
        $this->petugas();
        $id = $this->postJson('/api/v1/buku', $this->payload())->json('data.id');
        $this->deleteJson('/api/v1/buku/'.$id)->assertNoContent();
        $this->getJson('/api/v1/buku/'.$id)->assertNotFound();
        $this->assertSoftDeleted('buku', ['id' => $id]);
        $this->assertDatabaseHas('riwayat_stok', ['buku_id' => $id]);
    }

    public function test_parameter_daftar_dibatasi_dan_sort_tidak_bisa_injeksi(): void
    {
        $this->getJson('/api/v1/buku?per_page=1000&sort=judul%20desc&page=0')->assertUnprocessable();
    }

    public function test_metadata_sukses_dan_cache_menghindari_request_ulang(): void
    {
        Cache::flush();
        Http::preventStrayRequests();
        Http::fake(['openlibrary.org/*' => Http::response(['docs' => [
            ['title' => 'Laskar Pelangi', 'author_name' => ['Andrea Hirata'], 'first_publish_year' => 2005, 'isbn' => ['9789793062792']],
        ]])]);
        $this->getJson('/api/v1/metadata?q=Laskar')->assertOk()->assertJsonPath('data.source', 'openlibrary')->assertJsonPath('data.cached', false)->assertJsonPath('data.items.0.judul', 'Laskar Pelangi');
        $this->getJson('/api/v1/metadata?q=Laskar')->assertOk()->assertJsonPath('data.cached', true);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['fields'] === 'key,title,author_name,first_publish_year,isbn' && $request['limit'] === 5);
    }

    public function test_metadata_gagal_retry_dan_fallback_tidak_dicache(): void
    {
        Cache::flush();
        Http::fakeSequence()->pushStatus(503)->pushStatus(503)->push(['docs' => []]);
        $this->getJson('/api/v1/metadata?q=Gagal')->assertOk()->assertJsonPath('data.fallback', true)->assertJsonPath('data.source', 'local');
        $this->getJson('/api/v1/metadata?q=Gagal')->assertOk()->assertJsonPath('data.fallback', false);
        Http::assertSentCount(3);
    }

    public function test_metadata_timeout_dan_format_rusak_fallback(): void
    {
        Cache::flush();
        Http::fake(fn () => throw new ConnectionException('timeout'));
        $this->getJson('/api/v1/metadata?q=Timeout')->assertOk()->assertJsonPath('data.fallback', true);
        Http::fake(['*' => Http::response(['tidak' => 'sesuai'])]);
        $this->getJson('/api/v1/metadata?q=Rusak')->assertOk()->assertJsonPath('data.fallback', true);
    }

    public function test_portal_siswa_dan_petugas_dapat_dibuka(): void
    {
        $this->get('/')->assertOk()->assertSee('Jelajahi koleksi')->assertSee('/js/perpustakaan.js');
        $this->get('/petugas')->assertOk()->assertSee('Ruang petugas');
    }

    public function test_seeder_memiliki_minimal_sepuluh_buku_dan_idempoten(): void
    {
        config(['services.demo.password' => 'SeederHanyaTes123!']);
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('buku', 10);
        $this->assertDatabaseCount('riwayat_stok', 10);
        $this->assertDatabaseCount('petugas', 1);
    }

    public function test_penerbit_dan_tahun_dokumen_disimpan_dan_ditampilkan(): void
    {
        $this->petugas();
        $response = $this->postJson('/api/v1/buku', $this->payload())->assertCreated()
            ->assertJsonPath('data.penerbit', 'Penerbit Uji')->assertJsonPath('data.tahun_terbit', 2020);
        $this->getJson('/api/v1/buku/'.$response->json('data.id'))->assertOk()
            ->assertJsonPath('data.penerbit', 'Penerbit Uji')->assertJsonPath('data.tahun_terbit', 2020);
    }

    public function test_validasi_dokumen_menolak_penerbit_kosong_tahun_dan_panjang_tidak_valid(): void
    {
        $this->petugas();
        $data = $this->payload();
        foreach ([['penerbit' => ''], ['tahun_terbit' => 1800], ['tahun_terbit' => (int) now()->format('Y') + 1],
            ['tahun_terbit' => 'tahun'], ['kode_buku' => str_repeat('A', 21)], ['judul' => str_repeat('A', 151)], ['penulis' => str_repeat('A', 101)]] as $invalid) {
            $this->postJson('/api/v1/buku', [...$data, ...$invalid])->assertUnprocessable()->assertJsonPath('error.code', 'VALIDATION_ERROR');
        }
        $this->assertDatabaseCount('buku', 0);
    }

    public function test_seluruh_contoh_buku_sesuai_dokumen_dan_tidak_menimpa_edit(): void
    {
        config(['services.demo.password' => 'SeederHanyaTes123!']);
        $this->seed();
        $rows = json_decode(file_get_contents(database_path('seeders/data/buku-dokumen.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($rows as $row) {
            $book = Buku::where('kode_buku', $row['kode_buku'])->firstOrFail();
            $this->assertSame($row['kategori'], $book->kategori->nama);
            unset($row['kategori']);
            $this->assertDatabaseHas('buku', $row);
        }
        Buku::where('kode_buku', 'BK001')->firstOrFail()->update(['penerbit' => 'Diubah petugas']);
        $this->seed();
        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK001', 'penerbit' => 'Diubah petugas']);
        $this->assertDatabaseCount('riwayat_stok', 10);
    }

    public function test_impor_mengarsipkan_hanya_contoh_lama_yang_belum_diubah(): void
    {
        config(['services.demo.password' => 'SeederHanyaTes123!']);
        $petugas = $this->petugas();
        $rows = json_decode(file_get_contents(database_path('seeders/data/buku-demo-lama.json')), true, flags: JSON_THROW_ON_ERROR);
        $legacy = [];
        foreach (array_slice($rows, 0, 2) as $row) {
            $kategori = Kategori::firstOrCreate(['nama' => $row['kategori']]);
            unset($row['kategori']);
            $buku = Buku::factory()->create([...$row, 'kategori_id' => $kategori->id, 'penerbit' => null, 'tahun_terbit' => null]);
            $buku->riwayatStok()->create(['petugas_id' => $petugas->id, 'total_sebelum' => 0, 'tersedia_sebelum' => 0,
                'total_sesudah' => $buku->jumlah_total, 'tersedia_sesudah' => $buku->jumlah_tersedia, 'versi_stok' => 1, 'alasan' => 'Data awal demo UTS']);
            $legacy[] = $buku;
        }
        $legacy[1]->update(['judul' => 'Sudah diubah pengguna']);
        $this->artisan('buku:gunakan-data-dokumen')->assertSuccessful();
        $this->assertSoftDeleted('buku', ['id' => $legacy[0]->id]);
        $this->assertDatabaseHas('buku', ['id' => $legacy[1]->id, 'judul' => 'Sudah diubah pengguna', 'deleted_at' => null]);
        $this->assertDatabaseHas('riwayat_stok', ['buku_id' => $legacy[0]->id]);
        $this->assertSame(11, Buku::count());
        $this->artisan('buku:gunakan-data-dokumen')->assertSuccessful();
        $this->assertSame(11, Buku::count());
    }

    public function test_kontrak_openapi_mencakup_semua_route_api_dan_versi_rilis(): void
    {
        $contract = Yaml::parseFile(base_path('docs/openapi.yaml'));
        $this->assertSame(trim(file_get_contents(base_path('VERSION'))), $contract['info']['version']);
        $documented = [];
        foreach ($contract['paths'] as $path => $operations) {
            foreach ($operations as $method => $operation) {
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $documented[] = strtoupper($method).' api/v1'.$path;
                }
            }
        }
        $actual = [];
        foreach (Route::getRoutes() as $route) {
            if (str_starts_with($route->uri(), 'api/v1/')) {
                foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                    $actual[] = $method.' '.$route->uri();
                }
            }
        }
        sort($documented);
        sort($actual);
        $this->assertCount(15, $actual);
        $this->assertSame($actual, $documented);
    }
}
