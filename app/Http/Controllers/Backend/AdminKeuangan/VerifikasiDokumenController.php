<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackendUnivUsulan\Usulan;

class VerifikasiDokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Usulan::with(['pegawai:id,nama_lengkap,nip', 'jabatanTujuan:id,jabatan'])
            ->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_usulan', $request->status);
        }

        // Filter berdasarkan jenis usulan
        if ($request->filled('jenis_usulan')) {
            $query->where('jenis_usulan', $request->jenis_usulan);
        }

        $usulans = $query->latest()->paginate(15)->withQueryString();

        // Data untuk filter
        $statusOptions = [
            'Menunggu Verifikasi' => 'Menunggu Verifikasi',
            'Dalam Proses' => 'Dalam Proses'
        ];

        $jenisUsulanOptions = Usulan::distinct('jenis_usulan')
            ->pluck('jenis_usulan', 'jenis_usulan')
            ->toArray();

        return view('backend.layouts.views.admin-keuangan.verifikasi-dokumen', compact(
            'usulans',
            'statusOptions',
            'jenisUsulanOptions'
        ));
    }
}
