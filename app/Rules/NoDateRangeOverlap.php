<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NoDateRangeOverlap implements ValidationRule
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startDate = $value; // Ini adalah tanggal_mulai
        $endDate = $this->request->input('tanggal_selesai');

        $query = DB::table('periode_usulans');

        // Jika sedang dalam mode edit, kecualikan data yang sedang diedit
        if ($this->request->route('periode_usulan')) {
            $query->where('id', '!=', $this->request->route('periode_usulan')->id);
        }

        $overlap = $query->where(function ($q) use ($startDate, $endDate) {
            $q->where('tanggal_mulai', '<=', $endDate)
              ->where('tanggal_selesai', '>=', $startDate);
        })->exists();

        if ($overlap) {
            $fail('Rentang tanggal yang dipilih tumpang tindih dengan periode yang sudah ada.');
        }
    }
}
