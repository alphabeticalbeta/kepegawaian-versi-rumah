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
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade'); // Yang dokumennya diakses
            $table->foreignId('accessor_id')->constrained('pegawais')->onDelete('cascade'); // Yang mengakses
            $table->string('document_field'); // Field dokumen yang diakses (sk_pangkat_terakhir, etc.)
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->timestamp('accessed_at');
            $table->timestamps();

            // Index untuk performa query
            $table->index(['pegawai_id', 'document_field']);
            $table->index(['accessor_id', 'accessed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
    }
};
