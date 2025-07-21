<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function aplikasi()
    {
        return view('frontend.layouts.layanan.aplikasi');
    }

    public function usulanKepegawaian()
    {
        return view('frontend.layouts.layanan.usulan-kepegawaian');
    }
}
