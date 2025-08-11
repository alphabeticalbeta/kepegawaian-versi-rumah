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
        Schema::table('jabatans', function (Blueprint $table) {
            // Hierarchy level hanya untuk:
            // 1. Dosen Fungsional (1-5)
            // 2. Tenaga Kependidikan Fungsional Tertentu (sample: 1-2)
            // Yang lain = null (non-hierarki)
            $table->integer('hierarchy_level')->nullable()->after('jabatan');

            // Index untuk performa query hierarchy
            $table->index(['jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropIndex(['jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
            $table->dropColumn('hierarchy_level');
        });
    }
};
