document.addEventListener("DOMContentLoaded",function(){const o={Dosen:["Dosen Fungsional","Dosen Fungsi Tambahan"],"Tenaga Kependidikan":["Tenaga Kependidikan Fungsional Tertentu","Tenaga Kependidikan Fungsional Umum","Tenaga Kependidikan Struktural","Tenaga Kependidikan Tugas Tambahan"]};window.populateJenisJabatan=function(){const t=document.getElementById("jenis_pegawai"),a=document.getElementById("jenis_jabatan"),s=document.getElementById("nama_jabatan");if(!t||!a)return;const e=t.value;a.innerHTML='<option value="">Pilih Jenis Jabatan</option>',o[e]&&o[e].forEach(i=>{const n=document.createElement("option");n.value=i,n.textContent=i,a.appendChild(n)}),s&&(s.value="",updatePreview())},window.updatePreview=function(){const t=document.getElementById("jenis_pegawai"),a=document.getElementById("jenis_jabatan"),s=document.getElementById("nama_jabatan"),e=document.getElementById("jabatan-preview");if(!e)return;const i=t?t.value:"",n=a?a.value:"",m=s?s.value:"";i&&n&&m?(e.innerHTML=`
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Preview Jabatan
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Jenis Pegawai:</span>
                            <span class="text-sm text-slate-800 font-semibold">${i}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Jenis Jabatan:</span>
                            <span class="text-sm text-slate-800 font-semibold">${n}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Nama Jabatan:</span>
                            <span class="text-sm text-slate-800 font-semibold">${m}</span>
                        </div>
                    </div>
                </div>
            `,e.classList.remove("hidden")):e.classList.add("hidden")};const d=document.getElementById("jenis_pegawai"),l=document.getElementById("jenis_jabatan"),c=document.getElementById("nama_jabatan");d&&d.addEventListener("change",function(){populateJenisJabatan(),updatePreview()}),l&&l.addEventListener("change",updatePreview),c&&c.addEventListener("input",updatePreview),d&&(populateJenisJabatan(),updatePreview())});
