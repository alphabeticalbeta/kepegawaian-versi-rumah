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
        Schema::table('pangkats', function (Blueprint $table) {
            // Hierarchy level untuk pangkat PNS: 1 = terendah, semakin tinggi angka = semakin tinggi pangkat
            $table->integer('hierarchy_level')->nullable()->after('pangkat');

            // Index untuk performa query hierarchy
            $table->index(['hierarchy_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pangkats', function (Blueprint $table) {
            $table->dropIndex(['hierarchy_level']);
            $table->dropColumn('hierarchy_level');
        });
    }
};
