var f=(o,e)=>()=>(e||o((e={exports:{}}).exports,e),e.exports);var g=f((p,r)=>{class d{constructor(){this.init()}init(){this.initializeFilePreview(),this.initializeImagePreview(),this.initializeFormValidation(),this.initializeTabs(),this.initializeAlerts()}initializeFilePreview(){window.previewUploadedFile=function(e,t){const i=e.files[0],s=document.getElementById(t),n=document.getElementById("progress-"+e.name);if(i){if(i.type!=="application/pdf"){alert("Hanya file PDF yang diperbolehkan!"),e.value="";return}if(i.size>2097152){alert("Ukuran file maksimal 2MB!"),e.value="";return}const a=(i.size/1024/1024).toFixed(2),c=i.name;if(n){n.classList.remove("hidden");let l=0;const u=n.querySelector("div div"),m=setInterval(()=>{l+=Math.random()*30,l>=100&&(l=100,clearInterval(m),setTimeout(()=>{n.classList.add("hidden")},1e3)),u.style.width=l+"%"},200)}s&&(s.innerHTML=`
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                    <i data-lucide="file-check" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-green-800 truncate">${c}</p>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-xs text-green-600">Ukuran: ${a} MB</span>
                                        <span class="text-xs text-green-600 flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                            Siap diupload
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFilePreview('${e.id}', '${t}')"
                                        class="flex-shrink-0 text-green-600 hover:text-green-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    `,s.classList.remove("hidden")),typeof lucide<"u"&&lucide.createIcons()}else s&&s.classList.add("hidden")},window.clearFilePreview=function(e,t){const i=document.getElementById(e),s=document.getElementById(t);i&&(i.value=""),s&&s.classList.add("hidden")}}initializeImagePreview(){window.previewImage=function(e){const t=e.files[0];if(t){if(!t.type.startsWith("image/")){alert("Hanya file gambar yang diperbolehkan!"),e.value="";return}if(t.size>2*1024*1024){alert("Ukuran file maksimal 2MB!"),e.value="";return}const i=new FileReader;i.onload=function(s){const n=e.closest(".relative").querySelector("img");n&&(n.src=s.target.result);const a=document.getElementById("preview-foto");if(a){const c=(t.size/1024/1024).toFixed(2);a.innerHTML=`
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                        <i data-lucide="image" class="w-4 h-4 text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-green-800 truncate">${t.name}</p>
                                        <div class="flex items-center gap-4 mt-1">
                                            <span class="text-xs text-green-600">Ukuran: ${c} MB</span>
                                            <span class="text-xs text-green-600 flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                Siap diupload
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearImagePreview('${e.id}', 'preview-foto')"
                                            class="flex-shrink-0 text-green-600 hover:text-green-800">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        `,a.classList.remove("hidden")}typeof lucide<"u"&&lucide.createIcons()},i.readAsDataURL(t)}},window.clearImagePreview=function(e,t){const i=document.getElementById(e),s=document.getElementById(t),n=i.closest(".relative").querySelector("img");if(i&&(i.value=""),s&&s.classList.add("hidden"),n){const a=n.getAttribute("data-original-src");a&&(n.src=a)}}}initializeFormValidation(){document.querySelectorAll("form").forEach(t=>{t.addEventListener("submit",function(i){console.log("Form submitted:",t.action)})})}initializeTabs(){const e=document.querySelectorAll("[data-tab]"),t=document.querySelectorAll("[data-tab-content]");e.forEach(i=>{i.addEventListener("click",function(){const s=this.getAttribute("data-tab");e.forEach(a=>a.classList.remove("active")),t.forEach(a=>a.classList.add("hidden")),this.classList.add("active");const n=document.querySelector(`[data-tab-content="${s}"]`);n&&n.classList.remove("hidden")})})}initializeAlerts(){setTimeout(()=>{document.querySelectorAll(".alert").forEach(t=>{(t.classList.contains("alert-success")||t.classList.contains("alert-error"))&&(t.style.transition="opacity 0.5s ease-out",t.style.opacity="0",setTimeout(()=>t.remove(),500))})},5e3)}}document.addEventListener("DOMContentLoaded",function(){new d});typeof r<"u"&&r.exports&&(r.exports=d)});export default g();
