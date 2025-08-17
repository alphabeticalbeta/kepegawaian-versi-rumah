{{-- components/dokumen-upload.blade.php --}}
{{-- Enhanced component untuk upload dokumen dengan sistem validasi --}}

<div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <i data-lucide="upload" class="w-5 h-5 mr-2 text-indigo-600"></i>
        Upload Dokumen Pendukung
        @php
            // Hitung total error di section dokumen
            $dokumenErrors = collect($catatanPerbaikan['dokumen_usulan'] ?? [])
                ->where('status', 'tidak_sesuai')
                ->count();
        @endphp
        @if($dokumenErrors > 0)
            <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                {{ $dokumenErrors }} perlu perbaikan
            </span>
        @endif
    </h3>

    {{-- Summary Error Alert untuk Dokumen --}}
    @if($dokumenErrors > 0)
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <i data-lucide="file-x" class="w-5 h-5 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-red-800">
                        Dokumen Perlu Diperbaiki
                    </h4>
                    <p class="text-xs text-red-700 mt-1">
                        {{ $dokumenErrors }} dokumen memerlukan perbaikan. Silakan periksa catatan di setiap dokumen yang bermasalah.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
        {{-- PAKTA INTEGRITAS (Wajib untuk semua jenjang) --}}
        @if($formConfig['required_documents']['pakta_integritas'])
            @php
                $paktaValidation = $catatanPerbaikan['dokumen_usulan']['pakta_integritas'] ?? null;
                $isPaktaInvalid = $paktaValidation && $paktaValidation['status'] === 'tidak_sesuai';

                $paktaExists = false;
                if (isset($usulan)) {
                    $paktaExists = !empty($usulan->data_usulan['dokumen_usulan']['pakta_integritas']['path']) ||
                                !empty($usulan->data_usulan['pakta_integritas']);
                }
            @endphp

            <div class="bg-gradient-to-br {{ $isPaktaInvalid ? 'from-red-50 to-red-100 border-red-300' : 'from-blue-50 to-indigo-50 border-blue-200' }} border rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="{{ $isPaktaInvalid ? 'bg-red-100' : 'bg-blue-100' }} p-2 rounded-lg mr-3">
                        <i data-lucide="award" class="w-5 h-5 {{ $isPaktaInvalid ? 'text-red-600' : 'text-blue-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <label for="pakta_integritas" class="block text-sm font-semibold {{ $isPaktaInvalid ? 'text-red-800' : 'text-gray-800' }}">
                            Pakta Integritas
                            @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                            @if($isPaktaInvalid)
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Perlu Perbaikan
                                </span>
                            @endif
                        </label>
                        <p class="text-xs {{ $isPaktaInvalid ? 'text-red-700' : 'text-gray-600' }}">Surat Pakta Integritas</p>
                    </div>
                </div>

                @if($paktaExists)
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'pakta_integritas']) }}"
                    target="_blank" class="text-xs {{ $isPaktaInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                        <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                    </a>
                @endif

                @if(!$isReadOnly)
                    <input type="file" name="pakta_integritas" id="pakta_integritas"
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                            {{ $isPaktaInvalid ? 'file:bg-red-100 file:text-red-700 hover:file:bg-red-200' : 'file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200' }}
                            file:cursor-pointer cursor-pointer"
                        @if(empty($usulan) || !$paktaExists) required @endif>
                    <p class="mt-2 text-xs {{ $isPaktaInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 1MB.</p>
                @endif

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isPaktaInvalid)
                    <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $paktaValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('pakta_integritas')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        @endif

        {{-- BUKTI KORESPONDENSI (Conditional based on jenjang) --}}
        @if($formConfig['required_documents']['bukti_korespondensi'])
            @php
                $korespondensiValidation = $catatanPerbaikan['dokumen_usulan']['bukti_korespondensi'] ?? null;
                $isKorespondensiInvalid = $korespondensiValidation && $korespondensiValidation['status'] === 'tidak_sesuai';

                $korespondensiExists = false;
                if (isset($usulan)) {
                    $korespondensiExists = !empty($usulan->data_usulan['dokumen_usulan']['bukti_korespondensi']['path']) ||
                                        !empty($usulan->data_usulan['bukti_korespondensi']);
                }
            @endphp

            <div class="bg-gradient-to-br {{ $isKorespondensiInvalid ? 'from-red-50 to-red-100 border-red-300' : 'from-blue-50 to-indigo-50 border-blue-200' }} border rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="{{ $isKorespondensiInvalid ? 'bg-red-100' : 'bg-blue-100' }} p-2 rounded-lg mr-3">
                        <i data-lucide="mail" class="w-5 h-5 {{ $isKorespondensiInvalid ? 'text-red-600' : 'text-blue-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <label for="bukti_korespondensi" class="block text-sm font-semibold {{ $isKorespondensiInvalid ? 'text-red-800' : 'text-gray-800' }}">
                            Bukti Korespondensi
                            @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                            @if($isKorespondensiInvalid)
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Perlu Perbaikan
                                </span>
                            @endif
                        </label>
                        <p class="text-xs {{ $isKorespondensiInvalid ? 'text-red-700' : 'text-gray-600' }}">Surat korespondensi dengan jurnal</p>
                    </div>
                </div>

                @if($korespondensiExists)
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'bukti_korespondensi']) }}"
                    target="_blank" class="text-xs {{ $isKorespondensiInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                        <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                    </a>
                @endif

                @if(!$isReadOnly)
                    <input type="file" name="bukti_korespondensi" id="bukti_korespondensi"
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                            {{ $isKorespondensiInvalid ? 'file:bg-red-100 file:text-red-700 hover:file:bg-red-200' : 'file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200' }}
                            file:cursor-pointer cursor-pointer"
                        @if(empty($usulan) || !$korespondensiExists) required @endif>
                    <p class="mt-2 text-xs {{ $isKorespondensiInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 1MB.</p>
                @endif

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isKorespondensiInvalid)
                    <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $korespondensiValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('bukti_korespondensi')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        @endif

        {{-- TURNITIN (Conditional based on jenjang) --}}
        @if($formConfig['required_documents']['turnitin'])
            @php
                $turnitinValidation = $catatanPerbaikan['dokumen_usulan']['turnitin'] ?? null;
                $isTurnitinInvalid = $turnitinValidation && $turnitinValidation['status'] === 'tidak_sesuai';

                $turnitinExists = false;
                if (isset($usulan)) {
                    $turnitinExists = !empty($usulan->data_usulan['dokumen_usulan']['turnitin']['path']) ||
                                    !empty($usulan->data_usulan['turnitin']);
                }
            @endphp

            <div class="bg-gradient-to-br {{ $isTurnitinInvalid ? 'from-red-50 to-red-100 border-red-300' : 'from-green-50 to-emerald-50 border-green-200' }} border rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="{{ $isTurnitinInvalid ? 'bg-red-100' : 'bg-green-100' }} p-2 rounded-lg mr-3">
                        <i data-lucide="shield-check" class="w-5 h-5 {{ $isTurnitinInvalid ? 'text-red-600' : 'text-green-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <label for="turnitin" class="block text-sm font-semibold {{ $isTurnitinInvalid ? 'text-red-800' : 'text-gray-800' }}">
                            Turnitin
                            @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                            @if($isTurnitinInvalid)
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Perlu Perbaikan
                                </span>
                            @endif
                        </label>
                        <p class="text-xs {{ $isTurnitinInvalid ? 'text-red-700' : 'text-gray-600' }}">Laporan similarity check</p>
                    </div>
                </div>

                @if($turnitinExists)
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'turnitin']) }}"
                    target="_blank" class="text-xs {{ $isTurnitinInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                        <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                    </a>
                @endif

                @if(!$isReadOnly)
                    <input type="file" name="turnitin" id="turnitin"
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                            {{ $isTurnitinInvalid ? 'file:bg-red-100 file:text-red-700 hover:file:bg-red-200' : 'file:bg-green-100 file:text-green-700 hover:file:bg-green-200' }}
                            file:cursor-pointer cursor-pointer"
                        @if(empty($usulan) || !$turnitinExists) required @endif>
                    <p class="mt-2 text-xs {{ $isTurnitinInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 1MB.</p>
                @endif

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isTurnitinInvalid)
                    <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $turnitinValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('turnitin')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        @endif

        {{-- UPLOAD ARTIKEL (Conditional based on jenjang) --}}
        @if($formConfig['required_documents']['upload_artikel'])
            @php
                $artikelValidation = $catatanPerbaikan['dokumen_usulan']['upload_artikel'] ?? null;
                $isArtikelInvalid = $artikelValidation && $artikelValidation['status'] === 'tidak_sesuai';

                $artikelExists = false;
                if (isset($usulan)) {
                    $artikelExists = !empty($usulan->data_usulan['dokumen_usulan']['upload_artikel']['path']) ||
                                    !empty($usulan->data_usulan['upload_artikel']);
                }
            @endphp

            <div class="bg-gradient-to-br {{ $isArtikelInvalid ? 'from-red-50 to-red-100 border-red-300' : 'from-purple-50 to-violet-50 border-purple-200' }} border rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="{{ $isArtikelInvalid ? 'bg-red-100' : 'bg-purple-100' }} p-2 rounded-lg mr-3">
                        <i data-lucide="file-text" class="w-5 h-5 {{ $isArtikelInvalid ? 'text-red-600' : 'text-purple-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <label for="upload_artikel" class="block text-sm font-semibold {{ $isArtikelInvalid ? 'text-red-800' : 'text-gray-800' }}">
                            Upload Artikel
                            @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                            @if($isArtikelInvalid)
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Perlu Perbaikan
                                </span>
                            @endif
                        </label>
                        <p class="text-xs {{ $isArtikelInvalid ? 'text-red-700' : 'text-gray-600' }}">File artikel lengkap</p>
                    </div>
                </div>

                @if($artikelExists)
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'upload_artikel']) }}"
                    target="_blank" class="text-xs {{ $isArtikelInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                        <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                    </a>
                @endif

                @if(!$isReadOnly)
                    <input type="file" name="upload_artikel" id="upload_artikel"
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                            {{ $isArtikelInvalid ? 'file:bg-red-100 file:text-red-700 hover:file:bg-red-200' : 'file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200' }}
                            file:cursor-pointer cursor-pointer"
                        @if(empty($usulan) || !$artikelExists) required @endif>
                    <p class="mt-2 text-xs {{ $isArtikelInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 1MB.</p>
                @endif

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isArtikelInvalid)
                    <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $artikelValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('upload_artikel')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        @endif
    </div>

    {{-- INFO UNTUK JENJANG RENDAH (OPSIONAL DOCS) with Error Awareness --}}
    @if($jenjangType === 'tenaga-pengajar-to-asisten-ahli')
        @php
            $hasDocErrors = $dokumenErrors > 0;
        @endphp
        <div class="mb-6 p-4 {{ $hasDocErrors ? 'bg-red-50 border-l-4 border-red-400' : 'bg-yellow-50 border-l-4 border-yellow-400' }} rounded-r-lg">
            <div class="flex">
                @if($hasDocErrors)
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3 mt-0.5"></i>
                @else
                    <i data-lucide="info" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                @endif
                <div class="ml-3">
                    @if($hasDocErrors)
                        <p class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Terdapat dokumen yang perlu diperbaiki untuk usulan Tenaga Pengajar ke Asisten Ahli.
                            Meskipun beberapa dokumen bersifat opsional, kelengkapan dan keakuratan akan memperkuat usulan Anda.
                        </p>
                    @else
                        <p class="text-sm text-yellow-700">
                            <strong>Catatan:</strong> Untuk usulan Tenaga Pengajar ke Asisten Ahli, beberapa dokumen bersifat opsional.
                            Namun melengkapi semua dokumen akan memperkuat usulan Anda.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- SYARAT KHUSUS GURU BESAR --}}
    @if($formConfig['show_syarat_khusus'] && $formConfig['required_documents']['bukti_syarat_guru_besar'])
        @php
            $syaratGuruBesarValidation = $catatanPerbaikan['syarat_khusus']['syarat_guru_besar'] ?? null;
            $isSyaratGuruBesarInvalid = $syaratGuruBesarValidation && $syaratGuruBesarValidation['status'] === 'tidak_sesuai';

            $buktiGuruBesarValidation = $catatanPerbaikan['dokumen_usulan']['bukti_syarat_guru_besar'] ?? null;
            $isBuktiGuruBesarInvalid = $buktiGuruBesarValidation && $buktiGuruBesarValidation['status'] === 'tidak_sesuai';

            $buktiGuruBesarExists = false;
            if (isset($usulan)) {
                $buktiGuruBesarExists = !empty($usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path']) ||
                                        !empty($usulan->data_usulan['bukti_syarat_guru_besar']);
            }
        @endphp

        <div class="space-y-4 pt-6 border-t {{ ($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid) ? 'border-red-200' : '' }}">
            <div class="flex items-center gap-3">
                <h4 class="text-md font-semibold {{ ($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid) ? 'text-red-800' : 'text-gray-800' }}">
                    Syarat Khusus Pengajuan Guru Besar
                </h4>
                @if($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                        Perlu Perbaikan
                    </span>
                @endif
            </div>

            <div>
                <label for="syarat_guru_besar" class="block text-sm font-semibold {{ $isSyaratGuruBesarInvalid ? 'text-red-800' : 'text-gray-800' }} mb-1">
                    Pilih Salah Satu Syarat
                    @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                    @if($isSyaratGuruBesarInvalid)
                        <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                            Perlu Perbaikan
                        </span>
                    @endif
                </label>
                <select name="syarat_guru_besar" id="syarat_guru_besar"
                    class="block w-full px-4 py-3 text-gray-900 border-2 bg-white rounded-xl shadow-sm
                        @if($isReadOnly) bg-gray-50 text-gray-600 @endif
                        @if($isSyaratGuruBesarInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-200 @endif"
                    @if($isReadOnly) disabled @endif
                    @if(!$isReadOnly) required @endif>
                    <option value="">-- Silakan Pilih --</option>
                    <option value="hibah" {{ old('syarat_guru_besar', $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '') == 'hibah' ? 'selected' : '' }}>
                        Pernah mendapatkan hibah penelitian
                    </option>
                    <option value="bimbingan" {{ old('syarat_guru_besar', $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '') == 'bimbingan' ? 'selected' : '' }}>
                        Pernah membimbing program doktor
                    </option>
                    <option value="pengujian" {{ old('syarat_guru_besar', $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '') == 'pengujian' ? 'selected' : '' }}>
                        Pernah menguji mahasiswa doktor
                    </option>
                    <option value="reviewer" {{ old('syarat_guru_besar', $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '') == 'reviewer' ? 'selected' : '' }}>
                        Sebagai reviewer jurnal internasional
                    </option>
                </select>

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isSyaratGuruBesarInvalid)
                    <div class="mt-2 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $syaratGuruBesarValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('syarat_guru_besar')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="keterangan_div" class="p-4 {{ ($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid) ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }} rounded-lg text-sm {{ ($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid) ? 'text-red-800' : 'text-blue-800' }}" style="display: none;">
                <p class="font-semibold">Keterangan Dokumen:</p>
                <p id="keterangan_text"></p>
            </div>

            <div id="bukti_container" style="display: none;">
                <label for="bukti_syarat_guru_besar" class="block text-sm font-semibold {{ $isBuktiGuruBesarInvalid ? 'text-red-800' : 'text-gray-800' }} mb-1">
                    Upload Bukti Pendukung
                    @if(!$isReadOnly)<span class="text-red-500">*</span>@endif
                    @if($isBuktiGuruBesarInvalid)
                        <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                            Perlu Perbaikan
                        </span>
                    @endif
                </label>

                @if($buktiGuruBesarExists)
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'bukti_syarat_guru_besar']) }}"
                    target="_blank" class="text-xs {{ $isBuktiGuruBesarInvalid ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline mt-1 inline-block mb-2">
                        <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                    </a>
                @endif

                @if(!$isReadOnly)
                    <input type="file" name="bukti_syarat_guru_besar" id="bukti_syarat_guru_besar"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold
                            {{ $isBuktiGuruBesarInvalid ? 'file:bg-red-50 file:text-red-700 hover:file:bg-red-100' : 'file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100' }}">
                    <p class="mt-1 text-xs {{ $isBuktiGuruBesarInvalid ? 'text-red-600' : 'text-gray-500' }}">File harus dalam format PDF, maksimal 1MB.</p>
                @endif

                {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                @if($isBuktiGuruBesarInvalid)
                    <div class="mt-2 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                        <div class="flex items-start gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                            <div>
                                <strong>Catatan Perbaikan:</strong><br>
                                {{ $buktiGuruBesarValidation['keterangan'] }}
                            </div>
                        </div>
                    </div>
                @endif

                @error('bukti_syarat_guru_besar')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    @endif

    {{-- Progress Summary untuk Dokumen --}}
    @php
        $totalDokumen = collect($formConfig['required_documents'] ?? [])->where(function($value) { return $value; })->count();
        if ($formConfig['show_syarat_khusus'] ?? false) {
            $totalDokumen += 2; // syarat_guru_besar + bukti_syarat_guru_besar
        }
        $dokumenFixed = $totalDokumen - $dokumenErrors;
        $progressPercentage = $totalDokumen > 0 ? ($dokumenFixed / $totalDokumen) * 100 : 100;
    @endphp

    @if($totalDokumen > 0)
        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-slate-800">
                    Status Kelengkapan Dokumen
                </h4>
                <span class="text-xs {{ $dokumenErrors > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                    {{ $dokumenFixed }}/{{ $totalDokumen }} dokumen valid
                </span>
            </div>

            <div class="w-full bg-slate-200 rounded-full h-3 mb-2">
                <div class="{{ $dokumenErrors > 0 ? 'bg-red-500' : 'bg-green-500' }} h-3 rounded-full transition-all duration-500"
                     style="width: {{ $progressPercentage }}%">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
                <span>{{ number_format($progressPercentage, 1) }}% selesai</span>
                @if($dokumenErrors > 0)
                    <span class="text-red-600 font-medium">{{ $dokumenErrors }} perlu perbaikan</span>
                @else
                    <span class="text-green-600 font-medium">Semua dokumen valid</span>
                @endif
            </div>
        </div>
    @endif
</div>
