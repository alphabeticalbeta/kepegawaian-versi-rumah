{{-- components/bkd-upload.blade.php --}}
{{-- Enhanced component untuk upload BKD dengan sistem validasi --}}

<div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 -m-8 mb-8 p-6 rounded-t-xl">
        <h2 class="text-2xl font-bold text-white flex items-center">
            <i data-lucide="file-check-2" class="w-6 h-6 mr-3"></i>
            Beban Kinerja Dosen (BKD)
            @php
                // Hitung total error di section BKD
                $bkdErrors = 0;
                if (isset($bkdSemesters) && !empty($bkdSemesters)) {
                    foreach($bkdSemesters as $bkd) {
                        $slug = $bkd['slug'];
                        $validation = $catatanPerbaikan['dokumen_usulan'][$slug] ?? null;
                        if ($validation && $validation['status'] === 'tidak_sesuai') {
                            $bkdErrors++;
                        }
                    }
                }
            @endphp
            @if($bkdErrors > 0)
                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                    {{ $bkdErrors }} perlu perbaikan
                </span>
            @endif
        </h2>
        <p class="text-white/90 mt-2">Upload laporan BKD untuk 4 semester terakhir sesuai periode usulan.</p>
    </div>

    {{-- Summary Error Alert untuk BKD --}}
    @if($bkdErrors > 0)
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <i data-lucide="file-x-2" class="w-5 h-5 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-red-800">
                        Laporan BKD Perlu Diperbaiki
                    </h4>
                    <p class="text-xs text-red-700 mt-1">
                        {{ $bkdErrors }} laporan BKD memerlukan perbaikan. Semua laporan BKD harus valid untuk melanjutkan usulan.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if(isset($bkdSemesters) && !empty($bkdSemesters))
            @foreach($bkdSemesters as $bkd)
                @php
                    $slug = $bkd['slug'];
                    $label = $bkd['label'];

                    // Cek validasi untuk BKD ini
                    $validation = $catatanPerbaikan['dokumen_usulan'][$slug] ?? null;
                    $isInvalid = $validation && $validation['status'] === 'tidak_sesuai';

                    // Cek apakah file sudah ada
                    $fileExists = isset($usulan) && !empty($usulan->data_usulan['dokumen_usulan'][$slug]['path']);
                @endphp

                <div class="bg-gradient-to-br {{ $isInvalid ? 'from-red-50 to-red-100 border-red-300' : 'from-teal-50 to-cyan-50 border-teal-200' }} border rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="{{ $isInvalid ? 'bg-red-100' : 'bg-teal-100' }} p-2 rounded-lg mr-3">
                            <i data-lucide="file-check" class="w-5 h-5 {{ $isInvalid ? 'text-red-600' : 'text-teal-600' }}"></i>
                        </div>
                        <div class="flex-1">
                            <label for="{{ $slug }}" class="block text-sm font-semibold {{ $isInvalid ? 'text-red-800' : 'text-gray-800' }}">
                                {{ $label }}
                                @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                                @if($isInvalid)
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </label>
                            <p class="text-xs {{ $isInvalid ? 'text-red-700' : 'text-gray-600' }}">Laporan BKD untuk semester ini</p>
                        </div>
                    </div>

                    @if($fileExists)
                        <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => $slug]) }}"
                           target="_blank" class="text-xs {{ $isInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                            <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                        </a>
                    @endif

                    @if(!$isReadOnly)
                        <input type="file" name="{{ $slug }}" id="{{ $slug }}"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                                   {{ $isInvalid ? 'file:bg-red-100 file:text-red-700 hover:file:bg-red-200' : 'file:bg-teal-100 file:text-teal-700 hover:file:bg-teal-200' }}
                                   file:cursor-pointer cursor-pointer"
                               @if(empty($usulan) || !$fileExists) required @endif>
                        <p class="mt-2 text-xs {{ $isInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 2MB.</p>
                    @endif

                    @error($slug)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                    {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                    @if($isInvalid)
                        <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                            <div class="flex items-start gap-2">
                                <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                                <div>
                                    <strong>Catatan Perbaikan:</strong><br>
                                    {{ $validation['keterangan'] }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Progress Summary untuk BKD --}}
            @php
                $totalBkd = count($bkdSemesters);
                $bkdFixed = $totalBkd - $bkdErrors;
                $bkdProgress = $totalBkd > 0 ? ($bkdFixed / $totalBkd) * 100 : 100;
            @endphp

            <div class="md:col-span-2 mt-4 bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-slate-800 flex items-center">
                        <i data-lucide="progress" class="w-4 h-4 mr-2"></i>
                        Status Kelengkapan BKD
                    </h4>
                    <span class="text-xs {{ $bkdErrors > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                        {{ $bkdFixed }}/{{ $totalBkd }} laporan valid
                    </span>
                </div>

                <div class="w-full bg-slate-200 rounded-full h-3 mb-2">
                    <div class="{{ $bkdErrors > 0 ? 'bg-red-500' : 'bg-teal-500' }} h-3 rounded-full transition-all duration-500"
                         style="width: {{ $bkdProgress }}%">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600">
                    <span>{{ number_format($bkdProgress, 1) }}% selesai</span>
                    @if($bkdErrors > 0)
                        <span class="text-red-600 font-medium">{{ $bkdErrors }} laporan perlu perbaikan</span>
                    @else
                        <span class="text-green-600 font-medium">Semua laporan BKD valid</span>
                    @endif
                </div>
            </div>

            {{-- Info Alert berdasarkan Status --}}
            <div class="md:col-span-2 mt-4">
                @if($bkdErrors > 0)
                    <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                        <div class="flex">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3 mt-0.5"></i>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong>Perhatian:</strong> Beberapa laporan BKD memerlukan perbaikan.
                                    Semua laporan BKD harus valid dan lengkap sesuai dengan periode 4 semester terakhir untuk melanjutkan proses usulan jabatan.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg">
                        <div class="flex">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3 mt-0.5"></i>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    <strong>Bagus!</strong> Semua laporan BKD sudah valid dan lengkap.
                                    Pastikan laporan mencakup aktivitas Tri Dharma perguruan tinggi selama 4 semester terakhir.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        @else
            {{-- Fallback jika tidak ada data BKD --}}
            <div class="md:col-span-2 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800">Data BKD Tidak Tersedia</h4>
                        <p class="text-xs text-yellow-700 mt-1">
                            Tidak dapat memuat field upload BKD. Pastikan periode usulan aktif dan sistem telah dikonfigurasi dengan benar.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Additional BKD Requirements Info --}}
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Persyaratan Laporan BKD</h4>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li class="flex items-start">
                        <i data-lucide="check" class="w-3 h-3 mr-2 mt-0.5 text-blue-600"></i>
                        Laporan harus mencakup kegiatan Tri Dharma perguruan tinggi
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check" class="w-3 h-3 mr-2 mt-0.5 text-blue-600"></i>
                        Format file PDF dengan ukuran maksimal 2MB per laporan
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check" class="w-3 h-3 mr-2 mt-0.5 text-blue-600"></i>
                        Laporan telah diverifikasi dan disetujui oleh atasan langsung
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check" class="w-3 h-3 mr-2 mt-0.5 text-blue-600"></i>
                        Semua aktivitas harus sesuai dengan standar BKD yang berlaku
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
