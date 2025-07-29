<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_role', function (Blueprint $table) {
            // Foreign key yang mengarah ke tabel 'pegawais'
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');

            // Foreign key yang mengarah ke tabel 'roles'
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');

            // Menetapkan primary key gabungan untuk mencegah duplikasi data
            $table->primary(['pegawai_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_role');
    }
};
