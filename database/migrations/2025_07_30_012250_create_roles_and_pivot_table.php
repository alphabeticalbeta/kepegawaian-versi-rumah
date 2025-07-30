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
        // Tabel untuk menyimpan daftar role
        Schema::create('roles', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <-- TAMBAHKAN BARIS INI
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Tabel pivot untuk menghubungkan pegawai dan role
        Schema::create('pegawai_role', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <-- TAMBAHKAN BARIS INI JUGA
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['pegawai_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_role');
        Schema::dropIfExists('roles');
    }
};
