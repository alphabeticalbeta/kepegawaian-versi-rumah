<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackendUnivUsulan\Usulan;

class RapatSenatController extends Controller
{
    public function index(Request $request)
    {
        $query = Usulan::with(['pegawai:id,nama_lengkap,nip', 'jabatanTujuan:id,jabatan'])
            ->whereHas('pegawai', function($q) {
                $q->where('jenis_pegawai', 'Dosen');
            });

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_usulan', $request->status);
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
        $statusOptions = [
            'Menunggu Review Senat' => 'Menunggu Review Senat',
            'Dalam Review Senat' => 'Dalam Review Senat',
            'Sudah Direview Senat' => 'Sudah Direview Senat'
        ];

        $jabatans = \App\Models\BackendUnivUsulan\Jabatan::where('jenis_jabatan', 'Dosen Fungsional')
            ->orWhere('jenis_jabatan', 'Dosen dengan Tugas Tambahan')
            ->get();

        $periods = Usulan::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('backend.layouts.views.tim-senat.rapat-senat', compact(
            'usulans',
            'statusOptions',
            'jabatans',
            'periods'
        ));
    }
}
