<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if the column already exists to prevent errors
        if (!Schema::hasColumn('jabatans', 'jenis_jabatan')) {
            Schema::table('jabatans', function (Blueprint $table) {
                // Tambahkan kolom jenis_jabatan setelah id
                $table->string('jenis_jabatan')->after('id');
            });
        }
    }

    public function down()
    {
        // Only drop the column if it exists
        if (Schema::hasColumn('jabatans', 'jenis_jabatan')) {
            Schema::table('jabatans', function (Blueprint $table) {
                $table->dropColumn('jenis_jabatan');
            });
        }
    }
};
