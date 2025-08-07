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
        Schema::table('periode_usulans', function (Blueprint $table) {
            // Tambahkan kolom untuk tanggal mulai perbaikan.
            // Tipe 'date' dan bisa null (nullable). Diletakkan setelah 'tanggal_selesai'.
            $table->date('tanggal_mulai_perbaikan')->nullable()->after('tanggal_selesai');

            // Tambahkan kolom untuk tanggal selesai perbaikan.
            $table->date('tanggal_selesai_perbaikan')->nullable()->after('tanggal_mulai_perbaikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_usulans', function (Blueprint $table) {
            // Jika di-rollback, hapus kedua kolom ini.
            $table->dropColumn(['tanggal_mulai_perbaikan', 'tanggal_selesai_perbaikan']);
        });
    }
};
