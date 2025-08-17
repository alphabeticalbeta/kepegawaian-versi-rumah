<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class SKPangkatController extends Controller
{
    public function index(Request $request)
    {
        $query = Usulan::with(['pegawai:id,nama_lengkap,nip', 'pangkatTujuan:id,pangkat'])
            ->where('jenis_usulan', 'SK Pangkat')
            ->where('status_usulan', 'Disetujui');

        // Filter berdasarkan periode
        if ($request->filled('periode')) {
            $query->whereYear('created_at', $request->periode);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter berdasarkan unit kerja
        if ($request->filled('unit_kerja')) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('unit_kerja_terakhir_id', $request->unit_kerja);
            });
        }

        $usulans = $query->latest()->paginate(20)->withQueryString();

        // Data untuk filter
        $periods = Usulan::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $statusPembayaranOptions = [
            'Belum Dibayar' => 'Belum Dibayar',
            'Sedang Diproses' => 'Sedang Diproses',
            'Sudah Dibayar' => 'Sudah Dibayar'
        ];

        $unitKerjas = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with('subUnitKerja.unitKerja')
            ->get()
            ->groupBy('subUnitKerja.unitKerja.nama');

        return view('backend.layouts.views.admin-keuangan.sk-pangkat', compact(
            'usulans',
            'periods',
            'statusPembayaranOptions',
            'unitKerjas'
        ));
    }
}
