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
            $table->enum('status_pangkat', ['PNS', 'PPPK', 'Non-ASN'])
                  ->default('PNS')
                  ->after('hierarchy_level')
                  ->comment('Status pangkat: PNS, PPPK, atau Non-ASN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pangkats', function (Blueprint $table) {
            $table->dropColumn('status_pangkat');
        });
    }
};
