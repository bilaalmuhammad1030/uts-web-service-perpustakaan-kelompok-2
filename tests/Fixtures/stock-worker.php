<?php

use App\Exceptions\BusinessConflict;
use App\Models\Buku;
use App\Models\Petugas;
use App\Services\BukuService;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (! str_ends_with((string) config('database.connections.mysql.database'), '_test')) {
    exit(2);
}
$delay = (float) $argv[4] - microtime(true);
if ($delay > 0) {
    usleep((int) ($delay * 1000000));
}
try {
    $app->make(BukuService::class)->stok(Buku::findOrFail($argv[1]), [
        'jumlah_total' => 10, 'jumlah_tersedia' => (int) $argv[3], 'versi_stok' => 1, 'alasan' => 'Uji dua proses bersamaan',
    ], Petugas::findOrFail($argv[2]));
    echo 'success';
} catch (BusinessConflict) {
    echo 'conflict';
}
