<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });
        Schema::create('admin', function (Blueprint $table): void {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('token_hash', 64)->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('buku', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('kode_buku', 30)->unique();
            $table->string('judul', 200)->index();
            $table->string('penulis', 150)->index();
            $table->text('deskripsi')->nullable();
            $table->string('isbn', 13)->nullable();
            $table->string('lokasi_rak', 50);
            $table->unsignedInteger('jumlah_total');
            $table->unsignedInteger('jumlah_tersedia');
            $table->unsignedInteger('versi_stok')->default(1);
            $table->timestamp('stok_diperbarui_at');
            $table->timestamps();
            $table->softDeletes();
        });
        // MySQL/MariaDB enforce this even when data is written outside the service.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE buku ADD CONSTRAINT buku_stok_valid CHECK (jumlah_tersedia >= 0 AND jumlah_tersedia <= jumlah_total)');
        }
        Schema::create('riwayat_stok', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->restrictOnDelete();
            $table->foreignId('admin_id')->constrained('admin')->restrictOnDelete();
            $table->unsignedInteger('total_sebelum');
            $table->unsignedInteger('tersedia_sebelum');
            $table->unsignedInteger('total_sesudah');
            $table->unsignedInteger('tersedia_sesudah');
            $table->unsignedInteger('versi_stok');
            $table->string('alasan', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['buku_id', 'versi_stok']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('buku');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('kategori');
    }
};
