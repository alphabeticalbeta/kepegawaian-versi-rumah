{{-- Action Buttons Component --}}
@php
    // Determine if usulan can be edited
    $canEdit = $canEdit ?? in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']);

    $isCompleted = in_array($usulan->status_usulan, [
        'Perlu Perbaikan',
        'Dikembalikan',
        'Diteruskan Ke Universitas',
        'Disetujui',
        'Direkomendasikan',
        'Ditolak'
    ]);
@endphp

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Validasi</h3>
            <p class="text-sm text-gray-600">
                @if($canEdit)
                    Pilih aksi yang akan dilakukan setelah validasi selesai.
                @else
                    Status usulan: {{ $usulan->status_usulan }}
                @endif
            </p>
        </div>

        <div class="flex gap-4">
            @if($canEdit)
                {{-- Editable Actions --}}
                <button type="submit"
                        onclick="submitValidation(event)"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700
                               flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                        </path>
                    </svg>
                    Simpan Validasi
                </button>

                <button type="button"
                        onclick="showReturnForm()"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                    Kembalikan untuk Revisi
                </button>

                <button type="submit" name="action_type" value="reject_proposal"
                        onclick="return confirm('Apakah Anda yakin ingin MENOLAK usulan ini? Aksi ini tidak dapat dibatalkan.')"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tolak Usulan
                </button>

                <button type="submit" name="action_type" value="approve_proposal"
                        onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI & MEREKOMENDASIKAN usulan ini?')"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Setujui & Rekomendasikan
                </button>

                <button type="button"
                        onclick="showForwardForm()"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700
                               flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                        </path>
                    </svg>
                    Kirim Usulan Ke Tim Penilai
                </button>
            @else
                {{-- Read-only Status Indicators --}}
                @if($usulan->status_usulan === 'Perlu Perbaikan')
                    <div class="flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan sudah dikembalikan ke pegawai untuk perbaikan</span>
                    </div>

                @elseif($usulan->status_usulan === 'Diteruskan Ke Universitas')
                    <div class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan sudah diteruskan ke tingkat universitas</span>
                    </div>

                @elseif($usulan->status_usulan === 'Direkomendasikan')
                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan telah selesai diproses - Direkomendasikan</span>
                    </div>

                @elseif($usulan->status_usulan === 'Ditolak')
                    <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan telah selesai diproses - Ditolak</span>
                    </div>

                @else
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Status: {{ $usulan->status_usulan }}</span>
                    </div>
                @endif

                {{-- Back to List Button --}}
                <a href="{{ route('backend.admin-univ-usulan.periode-usulan.pendaftar', $usulan->periode_usulan_id) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700
                          flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z">
                        </path>
                    </svg>
                    Kembali ke Daftar
                </a>
            @endif
        </div>
    </div>
</div>
