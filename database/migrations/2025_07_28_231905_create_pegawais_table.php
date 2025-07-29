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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();

            // Kolom Relasi
            $table->foreignId('pangkat_terakhir_id')->constrained('pangkats');
            $table->foreignId('jabatan_terakhir_id')->constrained('jabatans');
            $table->foreignId('unit_kerja_terakhir_id')->constrained('sub_sub_unit_kerjas');

            // Data Utama
            $table->json('role'); // Menyimpan multiple role dalam format JSON
            $table->string('jenis_pegawai'); // Dosen, Tenaga Kependidikan
            $table->string('nip', 18)->unique();
            $table->string('nuptk', 16)->nullable();
            $table->string('gelar_depan');
            $table->string('gelar_belakang');
            $table->string('nomor_kartu_pegawai');

            // Data Kelahiran & Pribadi
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('nomor_handphone');

            // Data Kepegawaian (TMT & SK)
            $table->date('tmt_cpns');
            $table->string('sk_cpns_terakhir'); // Path ke file
            $table->date('tmt_pns');
            $table->string('sk_pns_terakhir'); // Path ke file
            $table->date('tmt_pangkat');
            $table->string('sk_pangkat_terakhir'); // Path ke file
            $table->date('tmt_jabatan');
            $table->string('sk_jabatan_terakhir'); // Path ke file

            // Data Pendidikan
            $table->string('pendidikan_terakhir');
            $table->string('ijazah_terakhir'); // Path ke file
            $table->string('transkrip_nilai_terakhir'); // Path ke file
            $table->string('sk_penyetaraan_ijazah')->nullable(); // Path ke file, opsional
            $table->string('disertasi_thesis_terakhir')->nullable(); // Path ke file

            // Data Fungsional (jika dosen, dll)
            $table->text('mata_kuliah_diampu')->nullable();
            $table->text('ranting_ilmu_kepakaran')->nullable();
            $table->string('url_profil_sinta')->nullable();

            // Data Kinerja
            $table->enum('predikat_kinerja_tahun_pertama', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang', 'Perlu Perbaikan']);
            $table->string('skp_tahun_pertama'); // Path ke file
            $table->enum('predikat_kinerja_tahun_kedua', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang', 'Perlu Perbaikan']);
            $table->string('skp_tahun_kedua'); // Path ke file
            $table->float('nilai_konversi')->nullable();
            $table->string('pak_konversi')->nullable(); // Path ke file
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
