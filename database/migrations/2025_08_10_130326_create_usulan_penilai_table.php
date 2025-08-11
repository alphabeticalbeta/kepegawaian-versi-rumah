<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usulan_penilai', function (Blueprint $table) {
            // Kolom untuk menghubungkan ke tabel 'usulans'
            // onDelete('cascade') berarti jika usulan dihapus, catatan di sini juga ikut terhapus.
            $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');

            // Kolom untuk menghubungkan ke tabel 'pegawais' (sebagai penilai)
            $table->foreignId('penilai_id')->constrained('pegawais')->onDelete('cascade');

            // Menjadikan kedua kolom sebagai Primary Key.
            // Ini untuk memastikan satu penilai hanya bisa ditugaskan SEKALI untuk usulan yang sama.
            $table->primary(['usulan_id', 'penilai_id']);

            // Kolom opsional untuk melacak kapan penugasan dibuat.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usulan_penilai');
    }
};
