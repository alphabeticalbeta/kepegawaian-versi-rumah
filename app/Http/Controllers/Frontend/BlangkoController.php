<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlangkoController extends Controller
{
    public function blangko()
    {
        return view('frontend.layouts.blangko-surat');
    }
}
