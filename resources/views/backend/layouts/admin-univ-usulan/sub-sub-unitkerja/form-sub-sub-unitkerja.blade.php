@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($subSubUnitKerja) ? 'Edit Sub Sub Unit Kerja' : 'Tambah Sub Sub Unit Kerja')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-lg mx-auto p-6 rounded-md shadow bg-white">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3 w-full">
                <div class="w-full bg-gray-400 rounded-md p-4">
                    <h2 class="text-xl font-bold text-black leading-tight">
                        {{ isset($subSubUnitKerja) ? 'Edit' : 'Tambah' }} Sub Sub Unit Kerja
                    </h2>
                    <p class="text-sm text-black mt-1">
                        Silakan lengkapi data sub sub unit kerja secara lengkap dan benar.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ isset($subSubUnitKerja)
                        ? route('backend.admin-univ-usulan.sub-sub-unitkerja.update', $subSubUnitKerja)
                        : route('backend.admin-univ-usulan.sub-sub-unitkerja.store') }}"
              method="POST">
            @csrf
            @if(isset($subSubUnitKerja))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label class="block mb-1 font-medium">Unit Kerja</label>
                <select name="unit_kerja_id" id="unit_kerja_id" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach($unitKerjas as $unitKerja)
                        <option value="{{ $unitKerja->id }}"
                            {{ (old('unit_kerja_id', isset($subSubUnitKerja) ? $subSubUnitKerja->subUnitKerja->unit_kerja_id : '') == $unitKerja->id) ? 'selected' : '' }}>
                            {{ $unitKerja->nama }}
                        </option>
                    @endforeach
                </select>
                @error('unit_kerja_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Sub Unit Kerja</label>
                <select name="sub_unit_kerja_id" id="sub_unit_kerja_id" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Pilih Sub Unit Kerja --</option>
                    @foreach($subUnitKerjas as $subUnitKerja)
                        <option value="{{ $subUnitKerja->id }}"
                            {{ (old('sub_unit_kerja_id', $subSubUnitKerja->sub_unit_kerja_id ?? '') == $subUnitKerja->id) ? 'selected' : '' }}>
                            {{ $subUnitKerja->nama }}
                        </option>
                    @endforeach
                </select>
                @error('sub_unit_kerja_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama Sub Sub Unit Kerja</label>
                <input placeholder="Seksi Administrasi / Lab Komputer" type="text" name="nama"
                    value="{{ old('nama', $subSubUnitKerja->nama ?? '') }}"
                    class="w-full border px-3 py-2 rounded" required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center space-x-4 mt-6">
                <a href="{{ route('backend.admin-univ-usulan.sub-sub-unitkerja.index') }}"
                   class="px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-gray-500 text-white hover:bg-gray-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitKerjaSelect = document.getElementById('unit_kerja_id');
    const subUnitKerjaSelect = document.getElementById('sub_unit_kerja_id');

    unitKerjaSelect.addEventListener('change', function() {
        const unitKerjaId = this.value;

        // Clear sub unit kerja options
        subUnitKerjaSelect.innerHTML = '<option value="">-- Pilih Sub Unit Kerja --</option>';

        if (unitKerjaId) {
            // Fetch sub unit kerjas via AJAX
            fetch(`{{ route('backend.admin-univ-usulan.get-sub-unit-kerjas') }}?unit_kerja_id=${unitKerjaId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subUnitKerja => {
                        const option = document.createElement('option');
                        option.value = subUnitKerja.id;
                        option.textContent = subUnitKerja.nama;
                        subUnitKerjaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching sub unit kerjas:', error);
                });
        }
    });
});
</script>
@endsection
