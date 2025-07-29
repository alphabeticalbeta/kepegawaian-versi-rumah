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
            // Tambahkan kolom email setelah 'nama_lengkap', pastikan unik
            $table->string('email')->unique()->after('nama_lengkap');
            // Tambahkan kolom foto setelah 'email', bisa null
            $table->string('foto')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['email', 'foto']);
        });
    }
};
