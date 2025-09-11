<?php

namespace App\Imports;

use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Models\KepegawaianUniversitas\Jabatan;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importMode;
    protected $errors = [];
    protected $successCount = 0;
    protected $updateCount = 0;
    protected $createCount = 0;

    public function __construct($importMode = 'create_update')
    {
        $this->importMode = $importMode;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip if required fields are empty
            if (empty($row['nip']) || empty($row['jenis_pegawai']) || empty($row['status_kepegawaian'])) {
                return null;
            }

            // Check if pegawai already exists
            $existingPegawai = Pegawai::where('nip', $row['nip'])->first();

            // Handle based on import mode
            if ($existingPegawai && $this->importMode === 'create_only') {
                // Skip existing records in create_only mode
                return null;
            }

            if (!$existingPegawai && $this->importMode === 'update_only') {
                // Skip new records in update_only mode
                return null;
            }

            // Prepare data
            $data = $this->prepareData($row);

            if ($existingPegawai) {
                // Update existing pegawai
                $existingPegawai->update($data);
                $this->updateCount++;
                return null; // Don't create new model
            } else {
                // Create new pegawai
                $this->createCount++;
                return new Pegawai($data);
            }

        } catch (\Exception $e) {
            Log::error('Error importing pegawai row: ' . $e->getMessage(), [
                'row' => $row,
                'error' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Prepare data for import
     */
    protected function prepareData(array $row)
    {
        // Get foreign key IDs
        $pangkatId = $this->getPangkatId($row['pangkat_terakhir'] ?? '');
        $jabatanId = $this->getJabatanId($row['jabatan_terakhir'] ?? '', $row['jenis_pegawai'] ?? '');
        $unitKerjaId = $this->getUnitKerjaId($row['unit_kerja'] ?? '');

        return [
            'nip' => $this->convertScientificNotation($row['nip']),
            'nama_lengkap' => $row['nama_lengkap'] ?? 'Belum diisi',
            'email' => $row['email'] ?? 'belum@diisi.com',
            'jenis_pegawai' => $row['jenis_pegawai'],
            'status_kepegawaian' => $row['status_kepegawaian'],
            'gelar_depan' => $row['gelar_depan'] ?? '',
            'gelar_belakang' => $row['gelar_belakang'] ?? '',
            'tempat_lahir' => $row['tempat_lahir'] ?? 'Belum diisi',
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? '1990-01-01'),
            'jenis_kelamin' => $row['jenis_kelamin'] ?? 'Laki-Laki',
            'nomor_handphone' => $this->convertScientificNotation($row['nomor_handphone'] ?? 'Belum diisi'),
            'pangkat_terakhir_id' => $pangkatId,
            'jabatan_terakhir_id' => $jabatanId,
            'unit_kerja_id' => $unitKerjaId,
            'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? '',
            'nama_universitas_sekolah' => $row['nama_universitas_sekolah'] ?? '',
            'nama_prodi_jurusan' => $row['nama_prodi_jurusan'] ?? '',
            'tmt_cpns' => $this->parseDate($row['tmt_cpns'] ?? '1990-01-01'),
            'tmt_pns' => $this->parseDate($row['tmt_pns'] ?? '1990-01-01'),
            'tmt_pangkat' => $this->parseDate($row['tmt_pangkat'] ?? '1990-01-01'),
            'tmt_jabatan' => $this->parseDate($row['tmt_jabatan'] ?? '1990-01-01'),
            'nomor_kartu_pegawai' => $this->convertScientificNotation($row['nomor_kartu_pegawai'] ?? ''),
            'nuptk' => $this->convertScientificNotation($row['nuptk'] ?? ''),
            'mata_kuliah_diampu' => $row['mata_kuliah_diampu'] ?? '',
            'ranting_ilmu_kepakaran' => $row['ranting_ilmu_kepakaran'] ?? '',
            'url_profil_sinta' => $row['url_profil_sinta'] ?? '',
            'predikat_kinerja_tahun_pertama' => $row['predikat_kinerja_tahun_pertama'] ?? null,
            'predikat_kinerja_tahun_kedua' => $row['predikat_kinerja_tahun_kedua'] ?? null,
            'password' => Hash::make($this->convertScientificNotation($row['nip'])), // Default password is NIP
        ];
    }

    /**
     * Get pangkat ID by name
     */
    protected function getPangkatId($pangkatName)
    {
        if (empty($pangkatName)) {
            return Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1;
        }

        $pangkat = Pangkat::where('pangkat', $pangkatName)->first();
        return $pangkat ? $pangkat->id : (Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1);
    }

    /**
     * Get jabatan ID by name and jenis pegawai
     */
    protected function getJabatanId($jabatanName, $jenisPegawai)
    {
        if (empty($jabatanName)) {
            return Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1;
        }

        $jabatan = Jabatan::where('jabatan', $jabatanName)
                         ->where('jenis_pegawai', $jenisPegawai)
                         ->first();

        return $jabatan ? $jabatan->id : (Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1);
    }

    /**
     * Get unit kerja ID by name
     */
    protected function getUnitKerjaId($unitKerjaName)
    {
        if (empty($unitKerjaName)) {
            return SubSubUnitKerja::first()?->id ?? 1;
        }

        $unitKerja = SubSubUnitKerja::whereHas('subUnitKerja.unitKerja', function($q) use ($unitKerjaName) {
            $q->where('nama', $unitKerjaName);
        })->first();

        return $unitKerja ? $unitKerja->id : (SubSubUnitKerja::first()?->id ?? 1);
    }

    /**
     * Parse date string
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '1990-01-01') {
            return '1990-01-01';
        }

        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return '1990-01-01';
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nip' => 'required|string|max:18',
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'status_kepegawaian' => 'required|string',
            'email' => 'nullable|email',
            'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'tmt_pangkat' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date',
        ];
    }

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import statistics
     */
    public function getStatistics()
    {
        return [
            'created' => $this->createCount,
            'updated' => $this->updateCount,
            'errors' => count($this->errors),
            'failures' => count($this->failures()),
        ];
    }

    /**
     * Convert scientific notation to string
     */
    private function convertScientificNotation($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Convert to string first
        $value = (string) $value;

        // Check if it's in scientific notation
        if (preg_match('/^[\d,]+\.?\d*E[+-]\d+$/i', $value)) {
            // Convert scientific notation to decimal
            $number = (float) str_replace(',', '.', $value);
            $formatted = number_format($number, 0, '', '');
            return $formatted;
        }

        // Remove apostrophe prefix if present
        if (strpos($value, "'") === 0) {
            $value = substr($value, 1);
        }

        return $value;
    }
}
