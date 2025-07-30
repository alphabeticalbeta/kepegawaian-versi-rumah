@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Manajemen Role Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Role Pegawai</h1>
        <p class="text-sm text-gray-500 mt-1">Tetapkan satu atau lebih peran untuk setiap pegawai.</p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-center rounded-md shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- TABEL PEGAWAI --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-3">NIP</th>
                    <th scope="col" class="px-6 py-3">Role yang Dimiliki</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $pegawai)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover mr-4 border" src="{{ $pegawai->foto ? Storage::url($pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}" alt="Foto">
                            {{ $pegawai->nama_lengkap }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $pegawai->nip }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($pegawai->roles as $role)
                                    <span class="px-2 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">{{ $role->name }}</span>
                                @empty
                                    <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Belum ada role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @can('manage-roles')
                                {{-- Tombol "Kelola Role" akan muncul di sini --}}
                                <button>Kelola Role</button>
                            @else
                                {{-- Jika tidak punya hak akses, hanya tanda strip (-) yang muncul --}}
                                <span>-</span>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">
                            Tidak ada data pegawai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $pegawais->links() }}
    </div>
</div>

{{-- MODAL POPUP UNTUK EDIT ROLE --}}
<div id="role-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                <i data-lucide="user-cog" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Role untuk:</h3>
            <p id="modal-pegawai-name" class="text-sm text-gray-500 font-bold mt-1"></p>

            <form id="role-form" method="POST" class="mt-6">
                @csrf
                @method('PUT')

                <div id="roles-checkbox-container" class="space-y-4 text-left p-4 border rounded-md bg-gray-50 max-h-64 overflow-y-auto">
                    {{-- Checkbox akan di-generate oleh JavaScript di sini --}}
                    <p class="text-gray-400 text-center">Memuat role...</p>
                </div>

                <div class="items-center px-4 py-3 mt-6">
                    <button id="cancel-modal-btn" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md mr-2 hover:bg-gray-300">
                        Batal
                    </button>
                    <button id="submit-role-btn" type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('role-modal');
    const openModalButtons = document.querySelectorAll('.open-modal-btn');
    const cancelModalButton = document.getElementById('cancel-modal-btn');
    const modalPegawaiName = document.getElementById('modal-pegawai-name');
    const roleForm = document.getElementById('role-form');
    const rolesCheckboxContainer = document.getElementById('roles-checkbox-container');

    // Fungsi untuk membuka modal
    const openModal = (e) => {
        const pegawaiId = e.target.dataset.pegawaiId;
        const pegawaiName = e.target.dataset.pegawaiName;

        // Set nama pegawai di judul modal
        modalPegawaiName.textContent = pegawaiName;

        // Set action form
        const updateUrl = `{{ url('admin-univ-usulan/role-pegawai') }}/${pegawaiId}`;
        roleForm.setAttribute('action', updateUrl);

        // Tampilkan loading
        rolesCheckboxContainer.innerHTML = '<p class="text-gray-400 text-center">Memuat role...</p>';
        modal.classList.remove('hidden');

        // Ambil data role via Fetch API
        fetch(`{{ url('admin-univ-usulan/role-pegawai') }}/${pegawaiId}/edit`)
            .then(response => response.json())
            .then(data => {
                populateCheckboxes(data.allRoles, data.pegawaiRoleIds);
            })
            .catch(error => {
                console.error('Error:', error);
                rolesCheckboxContainer.innerHTML = '<p class="text-red-500 text-center">Gagal memuat role.</p>';
            });
    };

    // Fungsi untuk mengisi checkbox
    const populateCheckboxes = (allRoles, pegawaiRoleIds) => {
        rolesCheckboxContainer.innerHTML = ''; // Kosongkan container
        allRoles.forEach(role => {
            const isChecked = pegawaiRoleIds.includes(role.id) ? 'checked' : '';
            const checkboxHtml = `
                <label for="role-${role.id}" class="flex items-center space-x-3 p-3 hover:bg-gray-100 rounded-md cursor-pointer">
                    <input id="role-${role.id}" name="roles[]" type="checkbox" value="${role.id}" ${isChecked}
                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="text-gray-700 text-sm font-medium">${role.name}</span>
                </label>
            `;
            rolesCheckboxContainer.insertAdjacentHTML('beforeend', checkboxHtml);
        });
    };

    // Fungsi untuk menutup modal
    const closeModal = () => {
        modal.classList.add('hidden');
    };

    // Tambahkan event listener ke setiap tombol "Kelola Role"
    openModalButtons.forEach(button => {
        button.addEventListener('click', openModal);
    });

    // Event listener untuk tombol batal dan klik di luar area modal
    cancelModalButton.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
});
</script>
@endpush
