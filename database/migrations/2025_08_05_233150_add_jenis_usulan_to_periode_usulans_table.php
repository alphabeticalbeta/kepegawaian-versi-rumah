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
            Schema::table('periode_usulans', function (Blueprint $table) {
            // Menambahkan kolom 'jenis_usulan' setelah kolom 'nama_periode'
            // Berdasarkan controller, sepertinya ini adalah string.
            $table->string('jenis_usulan')->after('nama_periode');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_usulans', function (Blueprint $table) {
            // Jika migrasi di-rollback, hapus kolom ini
            $table->dropColumn('jenis_usulan');
        });
    }
};
