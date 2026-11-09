<?php

namespace App\Console\Commands;

use App\Models\Buku;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('buku:gunakan-data-dokumen')]
#[Description('Impor contoh buku dokumen dan arsipkan hanya contoh lama yang belum diubah')]
class GunakanDataDokumen extends Command
{
    public function handle(): int
    {
        $backupPath = null;
        $result = DB::transaction(function () use (&$backupPath): array {
            $books = Buku::withTrashed()->lockForUpdate()->get();
            if (! app()->runningUnitTests()) {
                File::ensureDirectoryExists(base_path('.local'));
                $backupPath = base_path('.local/sebelum-impor-dokumen-'.now()->format('Ymd-His').'-'.Str::random(6).'.json');
                $snapshot = ['waktu' => now()->toISOString(), 'buku' => $books->toArray(),
                    'kategori' => DB::table('kategori')->get()->toArray(),
                    'riwayat_stok' => DB::table('riwayat_stok')->get()->toArray()];
                if (File::put($backupPath, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) === false) {
                    throw new \RuntimeException('Cadangan gagal ditulis; impor dibatalkan.');
                }
            }
            $legacy = json_decode(file_get_contents(database_path('seeders/data/buku-demo-lama.json')), true, flags: JSON_THROW_ON_ERROR);
            $archived = 0;
            $preserved = 0;
            foreach ($legacy as $original) {
                $book = Buku::with('kategori')->where('kode_buku', $original['kode_buku'])->lockForUpdate()->first();
                if (! $book) {
                    continue;
                }
                $category = $original['kategori'];
                unset($original['kategori']);
                $unchanged = $book->versi_stok === 1 && $book->penerbit === null && $book->tahun_terbit === null
                    && $book->created_at->equalTo($book->updated_at) && $book->kategori->nama === $category;
                foreach ($original as $field => $value) {
                    $unchanged = $unchanged && $book->getAttribute($field) === $value;
                }
                $unchanged = $unchanged && $book->riwayatStok()->count() === 1
                    && $book->riwayatStok()->where('alasan', 'Data awal demo UTS')->exists();
                if ($unchanged) {
                    $book->delete();
                    $archived++;
                } else {
                    $preserved++;
                }
            }
            app(DatabaseSeeder::class)->run();

            return ['archived' => $archived, 'preserved' => $preserved, 'active' => Buku::count()];
        }, 3);
        if ($backupPath) {
            $this->info('Cadangan data sebelumnya: '.$backupPath);
        }
        $this->info('Contoh lama diarsipkan: '.$result['archived'].'; data lama yang telah diubah dipertahankan: '.$result['preserved'].'; buku aktif: '.$result['active']);
        $this->info('Kode dokumen yang sudah ada/diarsipkan tidak ditimpa. Riwayat dan akun admin tetap disimpan.');

        return self::SUCCESS;
    }
}
