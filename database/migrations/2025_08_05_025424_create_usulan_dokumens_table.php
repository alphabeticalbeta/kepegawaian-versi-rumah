<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsulanDokumensTable extends Migration
{
    public function up()
    {
        Schema::create('usulan_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');

            // GANTI: dari 'pegawai_id' menjadi 'diupload_oleh_id'
            $table->foreignId('diupload_oleh_id')
                  ->constrained('pegawais')
                  ->onDelete('restrict')
                  ->comment('ID pegawai yang upload dokumen');

            $table->string('nama_dokumen', 100);
            $table->string('path', 500);
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['usulan_id', 'created_at']);
            $table->index('diupload_oleh_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usulan_dokumens');
    }
}
