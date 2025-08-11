<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usulan_jabatan_senat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usulan_id');
            $table->unsignedBigInteger('anggota_senat_id'); // FK ke pegawais.id
            $table->enum('keputusan', ['direkomendasikan','belum_direkomendasikan'])->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('diputuskan_pada')->nullable();
            $table->timestamps();

            $table->unique(['usulan_id','anggota_senat_id']);

            $table->foreign('usulan_id')->references('id')->on('usulans')->onDelete('cascade');
            $table->foreign('anggota_senat_id')->references('id')->on('pegawais')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_jabatan_senat');
    }
};
