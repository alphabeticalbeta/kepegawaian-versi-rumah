<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Usulan Jabatan</h3>
        <p class="text-sm text-gray-600">Menampilkan {{ $usulans->count() }} usulan yang ditemukan.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-3">NIP</th>
                    <th scope="col" class="px-6 py-3">Jabatan Saat Ini</th>
                    <th scope="col" class="px-6 py-3">Jabatan Tujuan</th>
                    <th scope="col" class="px-6 py-3">Tgl. Usulan</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usulans as $usulan)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $usulan->pegawai->nama_lengkap }}</td>
                        <td class="px-6 py-4">{{ $usulan->pegawai->nip }}</td>
                        <td class="px-6 py-4">{{ $usulan->jabatanLama->jabatan }}</td>
                        <td class="px-6 py-4">{{ $usulan->jabatanTujuan->jabatan }}</td>
                        <td class="px-6 py-4">{{ $usulan->created_at->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $usulan->status_usulan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data usulan untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
