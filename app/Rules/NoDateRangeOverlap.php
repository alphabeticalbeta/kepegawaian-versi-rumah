<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class NoDateRangeOverlap implements Rule
{
    protected $request;
    protected $excludeId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request, $excludeId = null)
    {
        $this->request = $request;
        $this->excludeId = $excludeId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $jenisUsulan = $this->request->jenis_usulan;
        $tanggalMulai = $this->request->tanggal_mulai;
        $tanggalSelesai = $this->request->tanggal_selesai;

        // Jika tanggal selesai belum diisi, skip validasi
        if (!$tanggalSelesai) {
            return true;
        }

        $query = PeriodeUsulan::where('jenis_usulan', $jenisUsulan)
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhere(function ($subQ) use ($tanggalMulai, $tanggalSelesai) {
                      $subQ->where('tanggal_mulai', '<=', $tanggalMulai)
                           ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            });

        // Exclude periode yang sedang di-edit
        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $jenisUsulan = $this->request->jenis_usulan;
        $jenisUsulanText = ucwords(str_replace('-', ' ', $jenisUsulan));

        return "Periode tanggal yang dipilih overlapping dengan periode {$jenisUsulanText} yang sudah ada.";
    }
}
