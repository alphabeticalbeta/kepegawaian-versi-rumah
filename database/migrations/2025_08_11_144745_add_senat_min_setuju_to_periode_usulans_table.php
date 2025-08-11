<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Asumsi nama tabel: periode_usulans
        Schema::table('periode_usulans', function (Blueprint $table) {
            $table->unsignedInteger('senat_min_setuju')->default(1)->after('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('periode_usulans', function (Blueprint $table) {
            $table->dropColumn('senat_min_setuju');
        });
    }
};
