{{-- resources/views/backend/components/profile/profile-header.blade.php --}}
<div class="bg-white rounded-xl shadow-sm border mb-6 overflow-hidden">
    <div class="bg-gradient-to-r from-yellow-200 to-yellow-500 h-32"></div>
    <div class="px-6 pb-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 -mt-16">
            {{-- Photo --}}
            <div class="relative">
                <div class="w-32 h-32 rounded-xl overflow-hidden border-4 border-white shadow-lg">
                    <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) . '&size=128&background=6366f1&color=fff' }}"
                         alt="Foto Profil"
                         id="profile-photo"
                         class="w-full h-full object-cover">
                </div>
                @if($isEditing)
                    <label for="foto" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-lg cursor-pointer hover:bg-indigo-700 transition-colors shadow-lg">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </label>
                    <input type="file" id="foto" name="foto" class="hidden" accept="image/*"
                        onchange="previewProfilePhoto(this)">

                    {{-- Preview area untuk foto --}}
                    <div id="preview-foto" class="hidden mt-2"></div>
                @endif
            </div>

            {{-- Basic Info --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '' }}
                    {{ $pegawai->nama_lengkap }}
                    {{ $pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '' }}
                </h2>
                <p class="text-gray-600 mt-1">NIP: {{ $pegawai->nip }}</p>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium flex items-center gap-1">
                        <i data-lucide="briefcase" class="w-3 h-3"></i>
                        {{ $pegawai->jabatan?->jabatan ?? 'Belum diset' }}
                    </span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium flex items-center gap-1">
                        <i data-lucide="award" class="w-3 h-3"></i>
                        {{ $pegawai->pangkat?->pangkat ?? 'Belum diset' }}
                    </span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium flex items-center gap-1">
                        <i data-lucide="building" class="w-3 h-3"></i>
                        {{ $pegawai->unitKerja?->nama ?? 'Belum diset' }}
                    </span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium flex items-center gap-1">
                        <i data-lucide="user" class="w-3 h-3"></i>
                        {{ $pegawai->jenis_pegawai ?? 'Belum diset' }}
                    </span>
                </div>

                {{-- Status Indicator --}}
                @if($pegawai->status_kepegawaian)
                    <div class="mt-3">
                        @php
                            $statusClass = match($pegawai->status_kepegawaian) {
                                'Dosen PNS', 'Tenaga Kependidikan PNS' => 'bg-green-100 text-green-800 border-green-300',
                                'Dosen PPPK', 'Tenaga Kependidikan PPPK' => 'bg-blue-100 text-blue-800 border-blue-300',
                                default => 'bg-gray-100 text-gray-800 border-gray-300'
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClass }}">
                            <div class="w-2 h-2 rounded-full bg-current mr-2"></div>
                            {{ $pegawai->status_kepegawaian }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Action Button --}}
            <div class="flex-shrink-0">
                @if($isEditing)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('pegawai-unmul.profile.show') }}"
                           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan
                        </button>
                    </div>
                @else
                    <a href="{{ route('pegawai-unmul.profile.edit') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        Edit Profil
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@if($isEditing)
    @push('scripts')
    <script>
        function previewProfilePhoto(input) {
            const file = input.files[0];
            const previewElement = document.getElementById('preview-foto');
            const profilePhoto = document.getElementById('profile-photo');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePhoto.src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Show file info
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;

                previewElement.innerHTML = `
                    <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-check" class="w-4 h-4 text-blue-600"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-800">${fileName}</p>
                                <p class="text-xs text-blue-600">Ukuran: ${fileSize} MB</p>
                            </div>
                            <div class="text-green-600">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                `;
                previewElement.classList.remove('hidden');

                // Re-initialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                previewElement.classList.add('hidden');
            }
        }
    </script>
    @endpush
@endif
