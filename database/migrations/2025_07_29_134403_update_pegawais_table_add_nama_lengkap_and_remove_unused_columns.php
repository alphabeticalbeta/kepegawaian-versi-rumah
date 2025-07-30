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
            // Add missing column
            $table->string('nama_lengkap')->after('nuptk');

            // Remove unused columns
            $table->dropColumn([
                'role',
                'tmt_cpns',
                'sk_cpns',
                'tmt_pns',
                'sk_pns'
            ]);

            // Make some columns nullable to match the form
            $table->string('gelar_depan')->nullable()->change();
            $table->string('gelar_belakang')->nullable()->change();
            $table->string('nomor_kartu_pegawai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Add back the removed columns
            $table->json('role');
            $table->date('tmt_cpns');
            $table->string('sk_cpns');
            $table->date('tmt_pns');
            $table->string('sk_pns');

            // Remove the added column
            $table->dropColumn('nama_lengkap');

            // Revert nullable changes
            $table->string('gelar_depan')->nullable(false)->change();
            $table->string('gelar_belakang')->nullable(false)->change();
            $table->string('nomor_kartu_pegawai')->nullable(false)->change();
        });
    }
};
