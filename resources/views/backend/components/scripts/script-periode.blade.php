{{-- Script untuk Form Periode Usulan --}}
@if(Request::is('admin-universitas-usulan/periode-usulan/*'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update info berdasarkan jenis usulan saat halaman load
    updateJenisUsulanInfo();

    // Validasi tanggal
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    const tanggalMulaiPerbaikan = document.getElementById('tanggal_mulai_perbaikan');
    const tanggalSelesaiPerbaikan = document.getElementById('tanggal_selesai_perbaikan');

    // Validasi tanggal selesai harus setelah tanggal mulai
    tanggalMulai.addEventListener('change', function() {
        tanggalSelesai.min = this.value;
        if (tanggalSelesai.value && tanggalSelesai.value < this.value) {
            tanggalSelesai.value = this.value;
        }
    });

    // Validasi tanggal perbaikan
    tanggalSelesai.addEventListener('change', function() {
        tanggalMulaiPerbaikan.min = this.value;
        if (tanggalMulaiPerbaikan.value && tanggalMulaiPerbaikan.value < this.value) {
            tanggalMulaiPerbaikan.value = this.value;
        }
    });

    tanggalMulaiPerbaikan.addEventListener('change', function() {
        tanggalSelesaiPerbaikan.min = this.value;
        if (tanggalSelesaiPerbaikan.value && tanggalSelesaiPerbaikan.value < this.value) {
            tanggalSelesaiPerbaikan.value = this.value;
        }
    });
});

function updateJenisUsulanInfo() {
    const jenisUsulan = document.getElementById('jenis_usulan').value;
    const infoBoxes = document.querySelectorAll('.info-box');

    // Sembunyikan semua info box
    infoBoxes.forEach(box => {
        box.classList.add('hidden');
    });

    // Tampilkan info box yang sesuai
    if (jenisUsulan === 'usulan-jabatan-dosen') {
        document.getElementById('info-dosen').classList.remove('hidden');
        document.getElementById('jenjang-dosen').classList.remove('hidden');
    } else if (jenisUsulan === 'usulan-jabatan-tendik') {
        document.getElementById('info-tendik').classList.remove('hidden');
        document.getElementById('warning-tendik').classList.remove('hidden');
        document.getElementById('jenjang-tendik').classList.remove('hidden');
    }
}
</script>
@endpush
@endif
