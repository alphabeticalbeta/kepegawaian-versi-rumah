{{-- Script untuk Usulan Jabatan Pegawai --}}
@if(Request::is('pegawai-unmul/usulan-jabatan/*'))
@push('scripts')
<script>
// EMERGENCY FIX: Disable problematic JavaScript dan focus on core functionality
function initializeForm() {
    console.log('Initializing form...');

    try {
        // FIXED: More specific form selection to avoid RadioNodeList conflict
        let form = null;

        // Try multiple approaches to find the actual form element
        const forms = document.forms;
        for (let i = 0; i < forms.length; i++) {
            if (forms[i].method.toLowerCase() === 'post') {
                form = forms[i];
                break;
            }
        }

        // Fallback: Try by action
        if (!form) {
            form = document.querySelector('form[action*="usulan-jabatan"]');
        }

        // Last resort: Get first form
        if (!form) {
            form = document.querySelector('form');
        }

        console.log('Form found:', form ? form.constructor.name : 'null');
        console.log('Form is HTMLFormElement:', form instanceof HTMLFormElement);

        if (form && form instanceof HTMLFormElement) {
            console.log('Valid form found, adding event listener');

            form.addEventListener('submit', function(e) {
                console.log('Form submitting...');

                // SIMPLIFIED: Remove client-side validation for now
                // Just let the form submit normally
                return true;
            });
        } else {
            console.warn('No valid HTMLFormElement found, skipping form validation');
        }

        // FIXED: Syarat Guru Besar handling - but with better error handling
        setTimeout(function() {
            try {
                const syaratSelect = document.getElementById('syarat_guru_besar');
                if (syaratSelect && syaratSelect.tagName === 'SELECT') {
                    console.log('Syarat guru besar select found');

                    const syaratDeskripsi = {
                        'hibah': 'Dokumen yang di-upload: MoU, SK Hibah, Laporan Hibah.',
                        'bimbingan': 'Dokumen yang di-upload: SK Pembimbing, Halaman Pengesahan, Cover Tesis yang dibimbing.',
                        'pengujian': 'Dokumen yang di-upload: SK Penguji, Berita Acara Hasil Ujian, Cover Tesis yang diuji.',
                        'reviewer': 'Dokumen yang di-upload: Surat Permohonan Reviewer, Dokumen yang di review.'
                    };

                    // Initialize display based on current value
                    updateSyaratDisplay(syaratSelect.value);

                    // CRITICAL FIX: Check if element is actually a form element
                    if (syaratSelect.addEventListener && typeof syaratSelect.addEventListener === 'function') {
                        syaratSelect.addEventListener('change', function() {
                            updateSyaratDisplay(this.value);
                        });
                    } else {
                        console.error('syaratSelect does not support addEventListener:', syaratSelect);
                    }

                    function updateSyaratDisplay(value) {
                        try {
                            console.log('updateSyaratDisplay called with value:', value);

                            const buktiContainer = document.getElementById('bukti_container');
                            const keteranganDiv = document.getElementById('keterangan_div');
                            const keteranganText = document.getElementById('keterangan_text');

                            console.log('Elements found:', {
                                buktiContainer: !!buktiContainer,
                                keteranganDiv: !!keteranganDiv,
                                keteranganText: !!keteranganText
                            });

                            if (buktiContainer) {
                                buktiContainer.style.display = value ? 'block' : 'none';
                            }

                            if (keteranganDiv) {
                                keteranganDiv.style.display = value ? 'block' : 'none';
                                if (keteranganText && syaratDeskripsi[value]) {
                                    keteranganText.textContent = syaratDeskripsi[value];
                                }
                            }
                        } catch (error) {
                            console.error('Error in updateSyaratDisplay:', error);
                        }
                    }
                } else {
                    console.log('Syarat guru besar select not found or not a SELECT element');
                }
            } catch (error) {
                console.error('Error in syarat guru besar initialization:', error);
            }
        }, 200);

        // SIMPLIFIED: Remove file upload validation that might be causing conflicts
        console.log('Skipping file upload validation to avoid conflicts');

    } catch (error) {
        console.error('Error in initializeForm:', error);
    }
}

// MULTIPLE DOM READY APPROACHES
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded via DOMContentLoaded');
        setTimeout(initializeForm, 100);
    });
} else {
    console.log('DOM already loaded');
    setTimeout(initializeForm, 100);
}

// Fallback approach
window.addEventListener('load', function() {
    console.log('Window loaded');
    setTimeout(initializeForm, 200);
});
</script>
@endpush
@endif
