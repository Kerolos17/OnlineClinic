// Booking Flow Enhancement Script

// Loading State Management
class LoadingManager {
    static show(element, text = null) {
        const isArabic = document.documentElement.dir === "rtl";
        const loadingText =
            text || (isArabic ? "جاري التحميل..." : "Loading...");

        element.disabled = true;
        element.dataset.originalContent = element.innerHTML;
        element.innerHTML = `
            <svg class="animate-spin ${
                isArabic ? "ml-2" : "mr-2"
            } h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>${loadingText}</span>
        `;
    }

    static hide(element) {
        element.disabled = false;
        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }
    }
}

// Form Validation Enhancement
class FormValidator {
    constructor(form) {
        this.form = form;
        this.isArabic = document.documentElement.dir === "rtl";
        this.init();
    }

    init() {
        const inputs = this.form.querySelectorAll("input, textarea, select");
        inputs.forEach((input) => {
            input.addEventListener("blur", () => this.validateField(input));
            input.addEventListener("input", () => this.clearError(input));
        });
    }

    validateField(field) {
        this.clearError(field);

        if (field.hasAttribute("required") && !field.value.trim()) {
            this.showError(
                field,
                this.isArabic ? "هذا الحقل مطلوب" : "This field is required"
            );
            return false;
        }

        if (field.type === "email" && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                this.showError(
                    field,
                    this.isArabic
                        ? "البريد الإلكتروني غير صحيح"
                        : "Invalid email address"
                );
                return false;
            }
        }

        if (field.type === "tel" && field.value) {
            const phoneRegex = /^[\d\s\+\-\(\)]+$/;
            if (
                !phoneRegex.test(field.value) ||
                field.value.replace(/\D/g, "").length < 10
            ) {
                this.showError(
                    field,
                    this.isArabic
                        ? "رقم الهاتف غير صحيح"
                        : "Invalid phone number"
                );
                return false;
            }
        }

        return true;
    }

    showError(field, message) {
        field.classList.add("border-red-500", "shake");
        setTimeout(() => field.classList.remove("shake"), 500);

        let errorDiv = field.parentElement.querySelector(".validation-error");
        if (!errorDiv) {
            errorDiv = document.createElement("p");
            errorDiv.className =
                "validation-error text-red-600 text-sm mt-1 flex items-center gap-1";
            field.parentElement.appendChild(errorDiv);
        }

        errorDiv.innerHTML = `
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>${message}</span>
        `;
    }

    clearError(field) {
        field.classList.remove("border-red-500");
        const errorDiv = field.parentElement.querySelector(".validation-error");
        if (errorDiv) errorDiv.remove();
    }

    validateAll() {
        const inputs = this.form.querySelectorAll(
            "input[required], textarea[required], select[required]"
        );
        let isValid = true;

        inputs.forEach((input) => {
            if (!this.validateField(input)) isValid = false;
        });

        return isValid;
    }
}

// Confirmation Dialog
class ConfirmationDialog {
    constructor(options) {
        this.options = options;
        this.isArabic = document.documentElement.dir === "rtl";
        this.create();
    }

    create() {
        const overlay = document.createElement("div");
        overlay.className =
            "fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fade-in";
        overlay.id = "confirmation-dialog";

        overlay.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-medical-900 mb-2">
                        ${
                            this.options.title ||
                            (this.isArabic ? "تأكيد الحجز" : "Confirm Booking")
                        }
                    </h3>
                    <p class="text-medical-600">
                        ${
                            this.options.message ||
                            (this.isArabic
                                ? "هل أنت متأكد من إتمام الحجز؟"
                                : "Are you sure you want to proceed with this booking?")
                        }
                    </p>
                </div>

                ${
                    this.options.details
                        ? `
                    <div class="bg-gradient-to-br from-medical-50 to-accent-50 rounded-xl p-4 mb-6 space-y-2 text-sm">
                        ${this.options.details}
                    </div>
                `
                        : ""
                }

                <div class="flex gap-3">
                    <button type="button" id="dialog-cancel" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        ${this.isArabic ? "إلغاء" : "Cancel"}
                    </button>
                    <button type="button" id="dialog-confirm" class="flex-1 px-6 py-3 bg-gradient-to-r from-medical-600 to-medical-700 hover:shadow-lg text-white font-semibold rounded-xl transition-all duration-200">
                        ${this.isArabic ? "تأكيد" : "Confirm"}
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        document
            .getElementById("dialog-cancel")
            .addEventListener("click", () => {
                this.close();
                if (this.options.onCancel) this.options.onCancel();
            });

        document
            .getElementById("dialog-confirm")
            .addEventListener("click", () => {
                this.close();
                if (this.options.onConfirm) this.options.onConfirm();
            });

        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) {
                this.close();
                if (this.options.onCancel) this.options.onCancel();
            }
        });
    }

    close() {
        const dialog = document.getElementById("confirmation-dialog");
        if (dialog) {
            dialog.classList.add("animate-fade-out");
            setTimeout(() => dialog.remove(), 200);
        }
    }
}

// Success Toast Notification
class Toast {
    static show(message, type = "success") {
        const isArabic = document.documentElement.dir === "rtl";
        const toast = document.createElement("div");

        const icons = {
            success:
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            warning:
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        };

        const colors = {
            success: "from-accent-500 to-accent-600",
            error: "from-red-500 to-red-600",
            warning: "from-yellow-500 to-yellow-600",
        };

        toast.className = `fixed ${
            isArabic ? "left-4" : "right-4"
        } top-4 z-50 animate-slide-in`;
        toast.innerHTML = `
            <div class="bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 max-w-md">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${icons[type]}
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("animate-slide-out");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", function () {
    const bookingForm = document.querySelector('form[action*="booking"]');
    if (bookingForm) {
        const validator = new FormValidator(bookingForm);

        bookingForm.addEventListener("submit", function (e) {
            e.preventDefault();

            if (!validator.validateAll()) {
                Toast.show(
                    document.documentElement.dir === "rtl"
                        ? "يرجى تصحيح الأخطاء في النموذج"
                        : "Please correct the errors in the form",
                    "error"
                );
                return;
            }

            const formData = new FormData(bookingForm);
            const isArabic = document.documentElement.dir === "rtl";

            new ConfirmationDialog({
                title: isArabic ? "تأكيد الحجز" : "Confirm Booking",
                message: isArabic
                    ? "يرجى مراجعة البيانات قبل المتابعة للدفع"
                    : "Please review your information before proceeding to payment",
                details: `
                    <div class="flex justify-between">
                        <span class="text-medical-600">${
                            isArabic ? "الاسم:" : "Name:"
                        }</span>
                        <span class="font-semibold text-medical-900">${formData.get(
                            "patient_name"
                        )}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-medical-600">${
                            isArabic ? "البريد:" : "Email:"
                        }</span>
                        <span class="font-semibold text-medical-900">${formData.get(
                            "patient_email"
                        )}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-medical-600">${
                            isArabic ? "الهاتف:" : "Phone:"
                        }</span>
                        <span class="font-semibold text-medical-900">${formData.get(
                            "patient_phone"
                        )}</span>
                    </div>
                `,
                onConfirm: () => {
                    const submitBtn = bookingForm.querySelector(
                        'button[type="submit"]'
                    );
                    LoadingManager.show(
                        submitBtn,
                        isArabic ? "جاري المعالجة..." : "Processing..."
                    );
                    bookingForm.submit();
                },
            });
        });
    }

    const inputs = document.querySelectorAll(".form-input");
    inputs.forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.classList.add("input-focused");
        });

        input.addEventListener("blur", function () {
            this.parentElement.classList.remove("input-focused");
        });
    });
});

window.LoadingManager = LoadingManager;
window.ConfirmationDialog = ConfirmationDialog;
window.Toast = Toast;
window.FormValidator = FormValidator;
