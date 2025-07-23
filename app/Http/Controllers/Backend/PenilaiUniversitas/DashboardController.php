<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Path view ini sudah benar mengarah ke resources/views/backend/
        return view('backend.layouts.penilai-universitas.dashboard');
    }
}
