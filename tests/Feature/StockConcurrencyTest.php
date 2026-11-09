<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Buku;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class StockConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Uji dua koneksi memerlukan MySQL/MariaDB; jalankan konfigurasi phpunit.mysql.xml.');
        }
        if (! str_ends_with((string) config('database.connections.mysql.database'), '_test')) {
            throw new \RuntimeException('Uji ini hanya diizinkan pada database berakhiran _test.');
        }
    }

    public function test_dua_proses_bersamaan_hanya_satu_berhasil(): void
    {
        $buku = Buku::factory()->create();
        $admin = Admin::factory()->create();
        $connection = config('database.connections.mysql');
        $environment = [
            'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_HOST' => $connection['host'],
            'DB_PORT' => (string) $connection['port'], 'DB_DATABASE' => $connection['database'],
            'DB_USERNAME' => $connection['username'], 'DB_PASSWORD' => $connection['password'] ?? '',
            'CACHE_STORE' => 'array',
        ];
        $startAt = microtime(true) + 2;
        $processes = [];
        foreach ([3, 5] as $tersedia) {
            $process = new Process([PHP_BINARY, base_path('tests/Fixtures/stock-worker.php'), (string) $buku->id, (string) $admin->id, (string) $tersedia, (string) $startAt], base_path(), $environment, timeout: 20);
            $process->start();
            $processes[] = $process;
        }
        $results = [];
        foreach ($processes as $process) {
            $process->wait();
            $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput().$process->getOutput());
            $results[] = trim($process->getOutput());
        }
        sort($results);
        $this->assertSame(['conflict', 'success'], $results);
        $this->assertSame(2, $buku->fresh()->versi_stok);
        $this->assertSame(1, DB::table('riwayat_stok')->where('buku_id', $buku->id)->count());
        $history = DB::table('riwayat_stok')->where('buku_id', $buku->id)->first();
        $this->assertSame($buku->fresh()->jumlah_tersedia, (int) $history->tersedia_sesudah);
    }
}
