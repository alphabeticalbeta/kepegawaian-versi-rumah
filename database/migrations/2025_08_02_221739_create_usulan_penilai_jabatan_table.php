<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_penilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('pegawais')->onDelete('cascade');
            $table->enum('status_penilaian', ['Belum Dinilai', 'Sesuai', 'Perlu Perbaikan'])->default('Belum Dinilai');
            $table->text('catatan_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_penilai');
    }
};
