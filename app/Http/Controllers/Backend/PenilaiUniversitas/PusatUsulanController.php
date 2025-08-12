<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PusatUsulanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil pegawai_id dari user login (sesuaikan cara ambil pegawai di projectmu)
        $pegawaiId = optional(Auth::user()->pegawai)->id;

        // Filter usulan yang ditugaskan ke penilai ini dari tabel usulan_penilai
        $usulanIds = DB::table('usulan_penilai')
            ->where('penilai_id', $pegawaiId)
            ->pluck('usulan_id');

        $query = Usulan::query()
            ->whereIn('id', $usulanIds)
            ->latest();

        // (opsional) filter status/keyword
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis_usulan', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', fn ($qq) => $qq->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $usulans = $query->paginate(15);

        return view('backend.layouts.penilai-universitas.pusat-usulan.index', compact('usulans'));
    }

    public function show(Usulan $usulan)
    {
        // Eager-load data yang dibutuhkan partial shared
        $usulan->load([
            'pegawai.pangkat',
            'pegawai.jabatan',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'jabatanLama',
            'jabatanTujuan',
            'periodeUsulan',
            'dokumens',
            'logs' => function ($query) {
                $query->with('dilakukanOleh')->latest();
            },
        ]);

        // (opsional) label BKD jika kamu sudah pakai di Admin Univ
        // $bkdLabels = $usulan->getBkdDisplayLabels();

        return view('backend.layouts.penilai-universitas.pusat-usulan.detail-usulan', [
            'usulan'    => $usulan,
            // 'bkdLabels' => $bkdLabels ?? [],
        ]);
    }
}
