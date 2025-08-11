<?php

namespace App\Helpers;

use App\Models\BackendUnivUsulan\Usulan;
use Carbon\Carbon;

class UsulanFieldHelper
{
    protected $usulan;

    public function __construct(Usulan $usulan)
    {
        $this->usulan = $usulan;
    }

    public function getFieldValue(string $category, string $field): string
    {
        switch ($category) {
            case 'data_pribadi':
            case 'data_kepegawaian':
            case 'data_pendidikan':
            case 'data_kinerja':
                return $this->getPegawaiData($field);

            case 'dokumen_profil':
                return $this->getDokumenProfilLink($field);

            case 'karya_ilmiah':
                return $this->getKaryaIlmiahData($field);

            case 'dokumen_usulan':
                return $this->getDokumenUsulanLink($field);

            // ADDED: Handle BKD documents
            case 'dokumen_bkd':
                return $this->getDokumenUsulanLink($field); // Use same method as dokumen_usulan

            default:
                return '-';
        }
    }

    protected function getPegawaiData(string $field): string
    {
        $pegawai = $this->usulan->pegawai;
        if (!$pegawai) return '-';

        // Menangani kasus khusus
        switch ($field) {
            case 'pangkat_saat_usul':
                return $pegawai->pangkat->pangkat ?? '-';
            case 'jabatan_saat_usul':
                return $pegawai->jabatan->jabatan ?? '-';
            case 'unit_kerja_saat_usul':
                return $pegawai->unitKerja->nama ?? '-';
            case 'tanggal_lahir':
            case 'tmt_pangkat':
            case 'tmt_jabatan':
            case 'tmt_cpns':
            case 'tmt_pns':
                $rawValue = $pegawai->{$field} ?? null;
                return $rawValue ? Carbon::parse($rawValue)->isoFormat('D MMMM YYYY') : '-';
            default:
                return $pegawai->{$field} ?? '-';
        }
    }

    protected function getDokumenProfilLink(string $field): string
    {
        $dokumenPath = $this->usulan->pegawai->{$field} ?? null;
        if (!empty($dokumenPath)) {
            $route = route('backend.admin-univ-usulan.data-pegawai.show-document', [$this->usulan->pegawai_id, $field]);
            return '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
        }
        return '<span class="text-red-500">✗ Belum diunggah</span>';
    }

    protected function getDokumenUsulanLink(string $field): string
    {
        // Metode getDocumentPath sudah ada di model Usulan, kita bisa manfaatkan
        $docPath = $this->usulan->getDocumentPath($field);

        if (!empty($docPath)) {
            // Perlu rute untuk menampilkan dokumen usulan dari admin universitas
            // Asumsikan rutenya ada atau akan dibuat
            $route = route('backend.admin-univ-usulan.pusat-usulan.show-document', [$this->usulan->id, $field]); // Sesuaikan nama rute jika perlu
            return '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
        }
        return '<span class="text-red-500">✗ Belum diunggah</span>';
    }

    protected function getKaryaIlmiahData(string $field): string
    {
        $data = $this->usulan->data_usulan['karya_ilmiah'] ?? [];

        if (str_starts_with($field, 'link_')) {
            $linkKey = str_replace('link_', '', $field);
            $linkValue = $data['links'][$linkKey] ?? $data[$field] ?? null;

            if ($linkValue && filter_var($linkValue, FILTER_VALIDATE_URL)) {
                return '<a href="' . $linkValue . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat Link</a>';
            }
            return $linkValue ?: '-';
        }

        if ($field === 'karya_ilmiah') {
            return $data['jenis_karya'] ?? $data['karya_ilmiah'] ?? '-';
        }

        return $data[$field] ?? '-';
    }
}
