<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('periode_usulan_id')->constrained('periode_usulans')->onDelete('cascade');

            // KOLOM BARU UNTUK MENANDAI JENIS USULAN
            $table->string('jenis_usulan');

            // Kolom ini bisa null karena tidak semua usulan terkait jabatan
            $table->foreignId('jabatan_lama_id')->nullable()->constrained('jabatans');
            $table->foreignId('jabatan_tujuan_id')->nullable()->constrained('jabatans');

            $table->string('status_usulan')->default('Draft');

            // Diubah menjadi nama generik untuk semua jenis data usulan
            $table->json('data_usulan')->nullable();

            $table->text('catatan_verifikator')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulans');
    }
};
