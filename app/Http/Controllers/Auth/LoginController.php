<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function username()
    {
        return 'nip';
    }


    public function __construct()
    {
        $this->middleware('guest:pegawai')->except('logout');
    }

    /**
     * Tentukan guard yang akan digunakan.
     */
    protected function guard()
    {
        return Auth::guard('pegawai');
    }

    /**
     * PERBAIKAN: Mengarahkan SEMUA pegawai ke dasbor pegawai sebagai default.
     *
     * @return string
     */
    public function redirectTo()
    {
        // Langsung arahkan ke dasbor pegawai tanpa pengecekan peran
        return route('pegawai-unmul.dashboard-pegawai-unmul');
    }
}
