class r{static show(e,t=null){const i=document.documentElement.dir==="rtl",o=t||(i?"جاري التحميل...":"Loading...");e.disabled=!0,e.dataset.originalContent=e.innerHTML,e.innerHTML=`
            <svg class="animate-spin ${i?"ml-2":"mr-2"} h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>${o}</span>
        `}static hide(e){e.disabled=!1,e.dataset.originalContent&&(e.innerHTML=e.dataset.originalContent,delete e.dataset.originalContent)}}class l{constructor(e){this.form=e,this.isArabic=document.documentElement.dir==="rtl",this.init()}init(){this.form.querySelectorAll("input, textarea, select").forEach(t=>{t.addEventListener("blur",()=>this.validateField(t)),t.addEventListener("input",()=>this.clearError(t))})}validateField(e){return this.clearError(e),e.hasAttribute("required")&&!e.value.trim()?(this.showError(e,this.isArabic?"هذا الحقل مطلوب":"This field is required"),!1):e.type==="email"&&e.value&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e.value)?(this.showError(e,this.isArabic?"البريد الإلكتروني غير صحيح":"Invalid email address"),!1):e.type==="tel"&&e.value&&(!/^[\d\s\+\-\(\)]+$/.test(e.value)||e.value.replace(/\D/g,"").length<10)?(this.showError(e,this.isArabic?"رقم الهاتف غير صحيح":"Invalid phone number"),!1):!0}showError(e,t){e.classList.add("border-red-500","shake"),setTimeout(()=>e.classList.remove("shake"),500);let i=e.parentElement.querySelector(".validation-error");i||(i=document.createElement("p"),i.className="validation-error text-red-600 text-sm mt-1 flex items-center gap-1",e.parentElement.appendChild(i)),i.innerHTML=`
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>${t}</span>
        `}clearError(e){e.classList.remove("border-red-500");const t=e.parentElement.querySelector(".validation-error");t&&t.remove()}validateAll(){const e=this.form.querySelectorAll("input[required], textarea[required], select[required]");let t=!0;return e.forEach(i=>{this.validateField(i)||(t=!1)}),t}}class c{constructor(e){this.options=e,this.isArabic=document.documentElement.dir==="rtl",this.create()}create(){const e=document.createElement("div");e.className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fade-in",e.id="confirmation-dialog",e.innerHTML=`
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-medical-900 mb-2">
                        ${this.options.title||(this.isArabic?"تأكيد الحجز":"Confirm Booking")}
                    </h3>
                    <p class="text-medical-600">
                        ${this.options.message||(this.isArabic?"هل أنت متأكد من إتمام الحجز؟":"Are you sure you want to proceed with this booking?")}
                    </p>
                </div>

                ${this.options.details?`
                    <div class="bg-gradient-to-br from-medical-50 to-accent-50 rounded-xl p-4 mb-6 space-y-2 text-sm">
                        ${this.options.details}
                    </div>
                `:""}

                <div class="flex gap-3">
                    <button type="button" id="dialog-cancel" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        ${this.isArabic?"إلغاء":"Cancel"}
                    </button>
                    <button type="button" id="dialog-confirm" class="flex-1 px-6 py-3 bg-gradient-to-r from-medical-600 to-medical-700 hover:shadow-lg text-white font-semibold rounded-xl transition-all duration-200">
                        ${this.isArabic?"تأكيد":"Confirm"}
                    </button>
                </div>
            </div>
        `,document.body.appendChild(e),document.getElementById("dialog-cancel").addEventListener("click",()=>{this.close(),this.options.onCancel&&this.options.onCancel()}),document.getElementById("dialog-confirm").addEventListener("click",()=>{this.close(),this.options.onConfirm&&this.options.onConfirm()}),e.addEventListener("click",t=>{t.target===e&&(this.close(),this.options.onCancel&&this.options.onCancel())})}close(){const e=document.getElementById("confirmation-dialog");e&&(e.classList.add("animate-fade-out"),setTimeout(()=>e.remove(),200))}}class d{static show(e,t="success"){const i=document.documentElement.dir==="rtl",o=document.createElement("div"),n={success:'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',error:'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',warning:'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'},a={success:"from-accent-500 to-accent-600",error:"from-red-500 to-red-600",warning:"from-yellow-500 to-yellow-600"};o.className=`fixed ${i?"left-4":"right-4"} top-4 z-50 animate-slide-in`,o.innerHTML=`
            <div class="bg-gradient-to-r ${a[t]} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 max-w-md">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${n[t]}
                </svg>
                <span class="font-medium">${e}</span>
            </div>
        `,document.body.appendChild(o),setTimeout(()=>{o.classList.add("animate-slide-out"),setTimeout(()=>o.remove(),300)},3e3)}}document.addEventListener("DOMContentLoaded",function(){const s=document.querySelector('form[action*="booking"]');if(s){const t=new l(s);s.addEventListener("submit",function(i){if(i.preventDefault(),!t.validateAll()){d.show(document.documentElement.dir==="rtl"?"يرجى تصحيح الأخطاء في النموذج":"Please correct the errors in the form","error");return}const o=new FormData(s),n=document.documentElement.dir==="rtl";new c({title:n?"تأكيد الحجز":"Confirm Booking",message:n?"يرجى مراجعة البيانات قبل المتابعة للدفع":"Please review your information before proceeding to payment",details:`
                    <div class="flex justify-between">
                        <span class="text-medical-600">${n?"الاسم:":"Name:"}</span>
                        <span class="font-semibold text-medical-900">${o.get("patient_name")}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-medical-600">${n?"البريد:":"Email:"}</span>
                        <span class="font-semibold text-medical-900">${o.get("patient_email")}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-medical-600">${n?"الهاتف:":"Phone:"}</span>
                        <span class="font-semibold text-medical-900">${o.get("patient_phone")}</span>
                    </div>
                `,onConfirm:()=>{const a=s.querySelector('button[type="submit"]');r.show(a,n?"جاري المعالجة...":"Processing..."),s.submit()}})})}document.querySelectorAll(".form-input").forEach(t=>{t.addEventListener("focus",function(){this.parentElement.classList.add("input-focused")}),t.addEventListener("blur",function(){this.parentElement.classList.remove("input-focused")})})});window.LoadingManager=r;window.ConfirmationDialog=c;window.Toast=d;window.FormValidator=l;
