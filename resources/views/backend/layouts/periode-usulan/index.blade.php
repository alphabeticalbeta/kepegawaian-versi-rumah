<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Data Periode Usulan</h3>
        <a href="{{ route('backend.admin-univ-usulan.periode-usulan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Tambah Periode
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Periode</th>
                    <th scope="col" class="px-6 py-3">Tahun</th>
                    <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3">Tanggal Selesai</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periodeUsulans as $periode)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $periode->nama_periode }}</td>
                        <td class="px-6 py-4">{{ $periode->tahun_periode }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-6 py-4">
                            @if($periode->status == 'Buka')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Buka</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tutup</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <a href="{{ route('backend.admin-univ-usulan.periode-usulan.edit', $periode->id) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('backend.admin-univ-usulan.periode-usulan.destroy', $periode->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Data periode usulan belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">
       {{ $periodeUsulans->links() }}
    </div>
</div>
