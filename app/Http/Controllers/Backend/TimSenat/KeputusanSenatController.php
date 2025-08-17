<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackendUnivUsulan\Usulan;

class KeputusanSenatController extends Controller
{
    public function index(Request $request)
    {
        $query = Usulan::with(['pegawai:id,nama_lengkap,nip', 'jabatanTujuan:id,jabatan'])
            ->whereHas('pegawai', function($q) {
                $q->where('jenis_pegawai', 'Dosen');
            })
            ->where('status_usulan', 'Sudah Direview Senat');

        // Filter berdasarkan keputusan
        if ($request->filled('keputusan')) {
            if ($request->keputusan === 'Disetujui') {
                $query->where('status_usulan', 'Disetujui');
            } elseif ($request->keputusan === 'Ditolak') {
                $query->where('status_usulan', 'Ditolak');
            }
        }

        // Filter berdasarkan jabatan
        if ($request->filled('jabatan')) {
            $query->where('jabatan_tujuan_id', $request->jabatan);
        }

        // Filter berdasarkan periode
        if ($request->filled('periode')) {
            $query->whereYear('created_at', $request->periode);
        }

        $usulans = $query->latest()->paginate(20)->withQueryString();

        // Data untuk filter
        $keputusanOptions = [
            'Disetujui' => 'Disetujui',
            'Ditolak' => 'Ditolak'
        ];

        $jabatans = \App\Models\BackendUnivUsulan\Jabatan::where('jenis_jabatan', 'Dosen Fungsional')
            ->orWhere('jenis_jabatan', 'Dosen dengan Tugas Tambahan')
            ->get();

        $periods = Usulan::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('backend.layouts.views.tim-senat.keputusan-senat', compact(
            'usulans',
            'keputusanOptions',
            'jabatans',
            'periods'
        ));
    }
}
