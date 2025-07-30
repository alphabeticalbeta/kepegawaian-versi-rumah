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
            // Menambahkan kolom-kolom yang hilang
            $table->date('tmt_cpns')->nullable()->after('nomor_handphone');
            $table->string('sk_cpns')->nullable()->after('tmt_cpns');
            $table->date('tmt_pns')->nullable()->after('sk_cpns');
            $table->string('sk_pns')->nullable()->after('tmt_pns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Menentukan urutan drop column agar tidak error
            $table->dropColumn([
                'tmt_cpns',
                'sk_cpns',
                'tmt_pns',
                'sk_pns'
            ]);
        });
    }
};
