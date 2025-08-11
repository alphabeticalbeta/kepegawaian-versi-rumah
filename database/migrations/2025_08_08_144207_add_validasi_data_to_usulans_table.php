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
        Schema::table('usulans', function (Blueprint $table) {
            // Tambah field JSON untuk menyimpan data validasi per role
            $table->json('validasi_data')->nullable()->after('data_usulan');

            // Index untuk performa query
            $table->index('status_usulan');
            $table->index('jenis_usulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            // Drop field yang ditambahkan
            $table->dropColumn('validasi_data');

            // Drop indexes
            $table->dropIndex(['status_usulan']);
            $table->dropIndex(['jenis_usulan']);
        });
    }
};
