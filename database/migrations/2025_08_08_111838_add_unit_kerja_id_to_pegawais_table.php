<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Menambahkan kolom baru untuk mengaitkan admin ke unit kerja (fakultas)
            // Kolom ini bisa NULL karena tidak semua pegawai adalah admin fakultas.
            // Kolom ini merujuk ke tabel 'unit_kerjas' (tabel fakultas).
            $table->foreignId('unit_kerja_id')
                  ->nullable()
                  ->after('id') // Posisi bisa disesuaikan
                  ->constrained('unit_kerjas')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['unit_kerja_id']);
            // Hapus kolomnya
            $table->dropColumn('unit_kerja_id');
        });
    }
};
