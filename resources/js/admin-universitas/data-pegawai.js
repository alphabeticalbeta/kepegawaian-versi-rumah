// Data Pegawai JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DATA PEGAWAI JS INITIALIZED ===');

    // Initialize global data
    window.dataPegawaiData = {
        activeTab: 'personal',
        totalTabs: 4,
        progress: 0,
        jenisPegawai: document.querySelector('#jenis_pegawai')?.value || '',
        formData: {}
    };

    // Add debug panel to page
    function addDebugPanel() {
        const debugPanel = document.createElement('div');
        debugPanel.id = 'debug-panel';
        debugPanel.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: #1f2937;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        `;

        debugPanel.innerHTML = `
            <div style="margin-bottom: 10px; font-weight: bold; color: #60a5fa;">Debug Panel</div>
            <div id="debug-content">
                <div>Jenis Pegawai: <span id="debug-jenis-pegawai">-</span></div>
                <div>Jenis Jabatan: <span id="debug-jenis-jabatan">-</span></div>
                <div>Visible Options: <span id="debug-visible-options">-</span></div>
                <div>Filter Status: <span id="debug-filter-status">-</span></div>
            </div>
            <div style="margin-top: 10px;">
                <button onclick="window.forceFilterJenisJabatan()" style="background: #059669; color: white; border: none; padding: 5px 10px; border-radius: 4px; margin-right: 5px; cursor: pointer;">Force Filter</button>
                <button onclick="window.testJenisJabatanFilter()" style="background: #dc2626; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Test Filter</button>
            </div>
        `;

        document.body.appendChild(debugPanel);
    }

    // Update debug panel
    function updateDebugPanel() {
        const jenisPegawaiEl = document.getElementById('jenis_pegawai');
        const jenisJabatanEl = document.getElementById('jenis_jabatan');

        if (jenisPegawaiEl && jenisJabatanEl) {
            const jenisPegawai = jenisPegawaiEl.value || 'Not selected';
            const jenisJabatan = jenisJabatanEl.value || 'Not selected';
            const visibleOptions = jenisJabatanEl.querySelectorAll('option:not([style*="display: none"])').length;

            document.getElementById('debug-jenis-pegawai').textContent = jenisPegawai;
            document.getElementById('debug-jenis-jabatan').textContent = jenisJabatan;
            document.getElementById('debug-visible-options').textContent = visibleOptions;
            document.getElementById('debug-filter-status').textContent = 'Active';
        }
    }

    // Tab switching functionality
    window.switchTab = function(tabName) {
        const currentTab = window.dataPegawaiData.activeTab;

        // Validate current tab before switching
        if (!validateCurrentTab(currentTab)) {
            return;
        }

        // Update active tab
        window.dataPegawaiData.activeTab = tabName;

        // Update Alpine.js data if available
        if (window.Alpine) {
            const component = document.querySelector('[x-data]').__x.$data;
            component.activeTab = tabName;
            component.updateProgress();
        }

        // Show success message
        showToast(`Berpindah ke tab ${getTabDisplayName(tabName)}`, 'success');
    };

    // Validate current tab
    function validateCurrentTab(tabName) {
        const requiredFields = {
            'personal': ['nama_lengkap', 'gelar_belakang'],
            'employment': ['jenis_pegawai', 'status_kepegawaian', 'pangkat_terakhir_id'],
            'dosen': ['mata_kuliah_diampu', 'ranting_ilmu_kepakaran'],
            'documents': []
        };

        const fields = requiredFields[tabName] || [];
        let isValid = true;
        let errorMessage = '';

        fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
                errorMessage = `Mohon lengkapi field ${fieldName.replace(/_/g, ' ')}`;
            } else if (field) {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            showToast(errorMessage, 'error');
        }

        return isValid;
    }

    // Get tab display name
    function getTabDisplayName(tabName) {
        const names = {
            'personal': 'Data Pribadi',
            'employment': 'Data Kepegawaian',
            'dosen': 'Data Dosen',
            'documents': 'Dokumen'
        };
        return names[tabName] || tabName;
    }

    // Update progress
    window.updateProgress = function() {
        const tabs = ['personal', 'employment', 'dosen', 'documents'];
        const activeIndex = tabs.indexOf(window.dataPegawaiData.activeTab);

        // Calculate progress based on filled fields
        let filledFields = 0;
        let totalFields = 0;

        // Count required fields across all tabs
        const allRequiredFields = [
            'nama_lengkap', 'gelar_belakang', 'jenis_pegawai',
            'status_kepegawaian', 'pangkat_terakhir_id', 'jabatan_terakhir_id'
        ];

        allRequiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                totalFields++;
                if (field.value.trim()) {
                    filledFields++;
                }
            }
        });

        const fieldProgress = totalFields > 0 ? (filledFields / totalFields) * 100 : 0;
        const tabProgress = ((activeIndex + 1) / window.dataPegawaiData.totalTabs) * 100;

        // Combine both progress indicators
        window.dataPegawaiData.progress = Math.round((fieldProgress + tabProgress) / 2);

        // Update progress bar
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.querySelector('.progress-text');

        if (progressBar) {
            progressBar.style.width = window.dataPegawaiData.progress + '%';
        }

        if (progressText) {
            progressText.textContent = `${window.dataPegawaiData.progress}%`;
        }

        // Update Alpine.js data if available
        if (window.Alpine) {
            const component = document.querySelector('[x-data]').__x.$data;
            component.formProgress = window.dataPegawaiData.progress;
        }
    };

    // Employment Data Filtering Functions
    window.filterJabatan = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jabatanSelect = document.getElementById('jabatan_terakhir_id');
        const selectedJabatanId = jabatanSelect.value;
        const previousJenisPegawai = jabatanSelect.getAttribute('data-previous-jenis-pegawai');

        if (!jenisPegawaiSelect || !jabatanSelect) {
            console.log('filterJabatan: Required elements not found');
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('filterJabatan: Filtering for jenis pegawai:', selectedJenisPegawai);

        const options = jabatanSelect.querySelectorAll('option');

        let shouldResetJabatan = false;
        let visibleOptions = 0;

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const jabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');
            console.log('filterJabatan: Option', option.textContent, 'has jenis_pegawai:', jabatanJenisPegawai);

            if (jabatanJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                if (option.value === selectedJabatanId) {
                    shouldResetJabatan = true;
                }
            }
        });

        console.log('filterJabatan: Visible options:', visibleOptions);

        // Only reset if jenis_pegawai actually changed and current jabatan is incompatible
        if (previousJenisPegawai && previousJenisPegawai !== selectedJenisPegawai && shouldResetJabatan) {
            jabatanSelect.value = '';
            console.log('filterJabatan: Reset jabatan selection');
        }

        jabatanSelect.setAttribute('data-previous-jenis-pegawai', selectedJenisPegawai);
    };

    window.filterStatusKepegawaian = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');
        const selectedStatusId = statusKepegawaianSelect.value;

        if (!jenisPegawaiSelect || !statusKepegawaianSelect) {
            console.log('filterStatusKepegawaian: Required elements not found');
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('filterStatusKepegawaian: Filtering for jenis pegawai:', selectedJenisPegawai);

        const options = statusKepegawaianSelect.querySelectorAll('option');

        let shouldResetStatus = false;
        let visibleOptions = 0;

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const statusJenisPegawai = option.getAttribute('data-jenis-pegawai');
            console.log('filterStatusKepegawaian: Option', option.textContent, 'has jenis_pegawai:', statusJenisPegawai);

            if (statusJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                if (option.value === selectedStatusId) {
                    shouldResetStatus = true;
                }
            }
        });

        console.log('filterStatusKepegawaian: Visible options:', visibleOptions);

        if (shouldResetStatus) {
            statusKepegawaianSelect.value = '';
            console.log('filterStatusKepegawaian: Reset status selection');
        }
    };

    window.filterJenisJabatan = function() {
        console.log('=== FILTER JENIS JABATAN CALLED ===');

        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');
        const selectedJenisJabatan = jenisJabatanSelect?.value;

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            console.log('filterJenisJabatan: Required elements not found');
            console.log('jenisPegawaiSelect:', !!jenisPegawaiSelect);
            console.log('jenisJabatanSelect:', !!jenisJabatanSelect);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('filterJenisJabatan: Filtering for jenis pegawai:', selectedJenisPegawai);
        console.log('filterJenisJabatan: Current selected jenis jabatan:', selectedJenisJabatan);

        const options = jenisJabatanSelect.querySelectorAll('option');
        console.log('filterJenisJabatan: Total options found:', options.length);

        let shouldResetJenisJabatan = false;
        let visibleOptions = 0;
        let hiddenOptions = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                console.log(`filterJenisJabatan: Option ${index} (placeholder) - keeping visible`);
                return;
            }

            const jenisJabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');
            console.log(`filterJenisJabatan: Option ${index} "${option.textContent}" has jenis_pegawai: "${jenisJabatanJenisPegawai}"`);

            if (jenisJabatanJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions++;
                console.log(`filterJenisJabatan: Option ${index} "${option.textContent}" - SHOWING (matches "${selectedJenisPegawai}")`);
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenOptions++;
                console.log(`filterJenisJabatan: Option ${index} "${option.textContent}" - HIDING (doesn't match "${selectedJenisPegawai}")`);
                if (option.value === selectedJenisJabatan) {
                    shouldResetJenisJabatan = true;
                    console.log(`filterJenisJabatan: Will reset selection because current selection "${selectedJenisJabatan}" is being hidden`);
                }
            }
        });

        console.log('filterJenisJabatan: Summary - Visible options:', visibleOptions, 'Hidden options:', hiddenOptions);

        if (shouldResetJenisJabatan) {
            jenisJabatanSelect.value = '';
            console.log('filterJenisJabatan: Reset jenis jabatan selection');
        }

        // Additional verification
        const visibleOptionsAfterFilter = jenisJabatanSelect.querySelectorAll('option:not([style*="display: none"])');
        console.log('filterJenisJabatan: Final visible options count:', visibleOptionsAfterFilter.length);

        // Update debug panel
        if (typeof updateDebugPanel === 'function') {
            updateDebugPanel();
        }

        console.log('=== FILTER JENIS JABATAN COMPLETED ===');
    };

    window.filterPangkat = function() {
        const statusKepegawaian = document.getElementById('status_kepegawaian');
        const pangkatSelect = document.getElementById('pangkat_terakhir_id');
        const selectedPangkatId = pangkatSelect.value;

        if (!statusKepegawaian || !pangkatSelect) return;

        const selectedStatus = statusKepegawaian.value;
        const options = pangkatSelect.querySelectorAll('option');

        let shouldResetPangkat = false;

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const pangkatStatus = option.getAttribute('data-status');
            let allowedPangkatStatuses = [];

            if (selectedStatus === 'Dosen PNS' || selectedStatus === 'Tenaga Kependidikan PNS') {
                allowedPangkatStatuses = ['PNS'];
            } else if (selectedStatus === 'Dosen PPPK' || selectedStatus === 'Tenaga Kependidikan PPPK') {
                allowedPangkatStatuses = ['PPPK'];
            } else if (selectedStatus === 'Dosen Non ASN' || selectedStatus === 'Tenaga Kependidikan Non ASN') {
                allowedPangkatStatuses = ['Non-ASN'];
            }

            if (allowedPangkatStatuses.includes(pangkatStatus)) {
                option.style.display = '';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                if (option.value === selectedPangkatId) {
                    shouldResetPangkat = true;
                }
            }
        });

        if (shouldResetPangkat) {
            pangkatSelect.value = '';
        }
    };

    // Photo preview functionality
    window.updatePhotoPreview = function(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const fotoPreview = document.getElementById('foto_preview');
                if (fotoPreview) {
                    fotoPreview.src = e.target.result;
                    fotoPreview.style.opacity = '1';
                }
            };
            reader.readAsDataURL(file);
        }
    };

    // Show toast notification
    window.showToast = function(message, type = 'info', duration = 3000) {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => {
            if (document.body.contains(toast)) {
                toast.remove();
            }
        });

        const toast = document.createElement('div');
        toast.className = `toast-notification fixed bottom-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl backdrop-blur-sm border transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white border-green-400' :
            type === 'error' ? 'bg-red-500 text-white border-red-400' :
            type === 'warning' ? 'bg-yellow-500 text-white border-yellow-400' :
            'bg-blue-500 text-white border-blue-400'
        }`;

        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                    type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                    type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
                </svg>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/80 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after duration
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.remove();
            }
        }, duration);
    };

    // Initialize event listeners
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const statusKepegawaianSelect = document.getElementById('status_kepegawaian');

    console.log('Initializing employment data filters...');
    console.log('jenisPegawaiSelect found:', !!jenisPegawaiSelect);
    console.log('statusKepegawaianSelect found:', !!statusKepegawaianSelect);

    if (jenisPegawaiSelect) {
        console.log('Current jenis pegawai value:', jenisPegawaiSelect.value);

        // Apply initial filtering based on current value
        if (jenisPegawaiSelect.value) {
            console.log('Applying initial filters for current jenis pegawai:', jenisPegawaiSelect.value);
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        }

        jenisPegawaiSelect.addEventListener('change', function() {
            console.log('jenis_pegawai changed to:', this.value);
            window.dataPegawaiData.jenisPegawai = this.value;

            // Update Alpine.js data if available
            if (window.Alpine) {
                const component = document.querySelector('[x-data]').__x.$data;
                component.jenisPegawai = this.value;
            }

            // Apply filters
            console.log('Applying filters...');
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();

            // Update progress
            updateProgress();

            // Update debug panel
            if (typeof updateDebugPanel === 'function') {
                updateDebugPanel();
            }
        });
    }

    if (statusKepegawaianSelect) {
        console.log('Current status kepegawaian value:', statusKepegawaianSelect.value);

        statusKepegawaianSelect.addEventListener('change', function() {
            console.log('status_kepegawaian changed to:', this.value);
            filterPangkat();
            updateProgress();
        });
    }

    // Function to check and fix filtering
    window.checkAndFixFiltering = function() {
        console.log('=== CHECKING AND FIXING FILTERING ===');

        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            console.log('ERROR: Elements not found');
            return false;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleCount = 0;
        let shouldBeVisible = 0;

        options.forEach(option => {
            if (option.value === '') return;

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');
            const isVisible = option.style.display !== 'none';

            if (dataJenisPegawai === selectedJenisPegawai) {
                shouldBeVisible++;
                if (isVisible) visibleCount++;
            }
        });

        console.log(`Filtering check: ${visibleCount}/${shouldBeVisible} correct options visible`);

        if (visibleCount !== shouldBeVisible) {
            console.log('Filtering is broken, fixing...');
            filterJenisJabatan();
            return true;
        }

        return false;
    };

    // Manual filter trigger function for debugging
    window.triggerFilters = function() {
        console.log('Manually triggering filters...');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        if (jenisPegawaiSelect) {
            console.log('Current jenis pegawai:', jenisPegawaiSelect.value);
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        } else {
            console.log('jenis_pegawai select not found');
        }
    };

    // Specific test function for jenis jabatan filtering
    window.testJenisJabatanFilter = function() {
        console.log('=== TESTING JENIS JABATAN FILTER ===');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            console.log('ERROR: Required elements not found');
            console.log('jenisPegawaiSelect:', !!jenisPegawaiSelect);
            console.log('jenisJabatanSelect:', !!jenisJabatanSelect);
            return;
        }

        console.log('Current jenis pegawai:', jenisPegawaiSelect.value);
        console.log('Current jenis jabatan:', jenisJabatanSelect.value);

        // Test with Dosen
        console.log('\n--- Testing with Dosen ---');
        jenisPegawaiSelect.value = 'Dosen';
        filterJenisJabatan();

        // Check visible options
        const visibleOptions = jenisJabatanSelect.querySelectorAll('option:not([style*="display: none"])');
        console.log('Visible options for Dosen:', visibleOptions.length);
        visibleOptions.forEach(opt => {
            console.log('  -', opt.textContent);
        });

        // Test with Tenaga Kependidikan
        console.log('\n--- Testing with Tenaga Kependidikan ---');
        jenisPegawaiSelect.value = 'Tenaga Kependidikan';
        filterJenisJabatan();

        // Check visible options
        const visibleOptions2 = jenisJabatanSelect.querySelectorAll('option:not([style*="display: none"])');
        console.log('Visible options for Tenaga Kependidikan:', visibleOptions2.length);
        visibleOptions2.forEach(opt => {
            console.log('  -', opt.textContent);
        });

        console.log('=== END TEST ===');
    };

    // Force filter jenis jabatan function
    window.forceFilterJenisJabatan = function() {
        console.log('=== FORCING JENIS JABATAN FILTER ===');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            console.log('ERROR: Required elements not found');
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('Forcing filter for jenis pegawai:', selectedJenisPegawai);

        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                console.log(`Option ${index} (placeholder) - keeping visible`);
                return;
            }

            const jenisJabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');
            console.log(`Option ${index} "${option.textContent}" has jenis_pegawai: "${jenisJabatanJenisPegawai}"`);

            if (jenisJabatanJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
                console.log(`Option ${index} "${option.textContent}" - FORCED SHOW`);
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
                console.log(`Option ${index} "${option.textContent}" - FORCED HIDE`);
            }
        });

        console.log(`Force filter complete: ${visibleCount} visible, ${hiddenCount} hidden`);
        console.log('=== END FORCE FILTER ===');
    };

    // Initialize filters on page load with delay to ensure DOM is ready
    console.log('Initializing filters on page load...');

    // Immediate initialization
    setTimeout(() => {
        console.log('=== IMMEDIATE INITIALIZATION (100ms) ===');
        if (jenisPegawaiSelect) {
            console.log('Immediate init - Current jenis pegawai:', jenisPegawaiSelect.value);
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();

            // Force apply jenis jabatan filter if there's a current value
            if (jenisPegawaiSelect.value) {
                console.log('Forcing jenis jabatan filter on immediate initialization');
                window.forceFilterJenisJabatan();
            }
        } else {
            console.log('jenisPegawaiSelect not found in immediate initialization');
        }
    }, 100);

    // Delayed initialization
    setTimeout(() => {
        console.log('=== DELAYED INITIALIZATION (500ms) ===');
        if (jenisPegawaiSelect) {
            console.log('Delayed init - Current jenis pegawai:', jenisPegawaiSelect.value);
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();

            // Force apply jenis jabatan filter if there's a current value
            if (jenisPegawaiSelect.value) {
                console.log('Forcing jenis jabatan filter on delayed initialization');
                window.forceFilterJenisJabatan();
            }
        } else {
            console.log('jenisPegawaiSelect not found in delayed initialization');
        }
    }, 500);

    // Final initialization with check
    setTimeout(() => {
        console.log('=== FINAL INITIALIZATION (1000ms) ===');
        if (jenisPegawaiSelect) {
            console.log('Final init - Current jenis pegawai:', jenisPegawaiSelect.value);
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();

            // Check and fix if needed
            window.checkAndFixFiltering();

            // Update debug panel
            if (typeof updateDebugPanel === 'function') {
                updateDebugPanel();
            }
        } else {
            console.log('jenisPegawaiSelect not found in final initialization');
        }
    }, 1000);

    // Periodic check every 2 seconds for the first 10 seconds
    let checkCount = 0;
    const checkInterval = setInterval(() => {
        checkCount++;
        console.log(`=== PERIODIC CHECK ${checkCount} ===`);

        if (jenisPegawaiSelect && jenisPegawaiSelect.value) {
            window.checkAndFixFiltering();
        }

        if (checkCount >= 5) {
            clearInterval(checkInterval);
            console.log('Periodic checks completed');
        }
    }, 2000);

    // Form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            // Validate all tabs before submission
            const tabs = ['personal', 'employment', 'dosen', 'documents'];
            let hasErrors = false;

            tabs.forEach(tab => {
                if (!validateCurrentTab(tab)) {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                e.preventDefault();
                showToast('Mohon lengkapi semua field wajib sebelum menyimpan', 'error');

                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });
    }

    // Real-time progress updates
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            updateProgress();
        });

        input.addEventListener('change', function() {
            updateProgress();
        });
    });

    // Initialize progress on page load
    setTimeout(() => {
        updateProgress();
    }, 100);

    // Show success message if available
    if (window.successMessage) {
        showToast(window.successMessage, 'success');
    }

    // Add debug panel on page load
    addDebugPanel();
    updateDebugPanel();
});
