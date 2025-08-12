<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usulan_validasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');
            $table->string('role', 50); // ex: admin_fakultas, penilai_universitas, admin_universitas, senat
            $table->json('data')->nullable(); // semua hasil validasi (fleksibel)
            $table->unsignedBigInteger('submitted_by')->nullable(); // FK ke pegawais.id atau users.id
            $table->timestamps();

            $table->unique(['usulan_id', 'role']); // 1 baris per role per usulan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_validasi');
    }
};
