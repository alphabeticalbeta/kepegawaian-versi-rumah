<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function visiMisi()
    {
        return view('frontend.layouts.profil.visi-misi');
    }

    public function strukturOrganisasi()
    {
        return view('frontend.layouts.profil.struktur-organisasi');
    }
}
