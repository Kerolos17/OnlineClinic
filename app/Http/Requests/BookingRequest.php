<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'slot_id' => ['required', 'exists:slots,id'],
            'patient_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\p{L}\s\-\']+$/u', // Letters, spaces, hyphens, apostrophes only
            ],
            'patient_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'patient_phone' => [
                'required',
                'regex:/^\+?[1-9]\d{1,14}$/', // E.164 format
            ],
            'patient_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'patient_name.required' => 'الاسم مطلوب',
                'patient_name.min' => 'الاسم يجب أن يكون 3 أحرف على الأقل',
                'patient_name.regex' => 'الاسم يجب أن يحتوي على حروف فقط',
                'patient_email.required' => 'البريد الإلكتروني مطلوب',
                'patient_email.email' => 'البريد الإلكتروني غير صحيح',
                'patient_phone.required' => 'رقم الهاتف مطلوب',
                'patient_phone.regex' => 'رقم الهاتف غير صحيح',
                'slot_id.exists' => 'الموعد المحدد غير متاح',
            ];
        }

        return [
            'patient_name.required' => 'Name is required',
            'patient_name.min' => 'Name must be at least 3 characters',
            'patient_name.regex' => 'Name must contain only letters',
            'patient_email.required' => 'Email is required',
            'patient_email.email' => 'Invalid email address',
            'patient_phone.required' => 'Phone number is required',
            'patient_phone.regex' => 'Invalid phone number format',
            'slot_id.exists' => 'Selected slot is not available',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'patient_name' => strip_tags(trim($this->patient_name ?? '')),
            'patient_notes' => strip_tags(trim($this->patient_notes ?? '')),
            'patient_email' => strtolower(trim($this->patient_email ?? '')),
            'patient_phone' => preg_replace('/\s+/', '', $this->patient_phone ?? ''),
        ]);
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Additional validation: Check if slot is still available
        if (isset($validated['slot_id'])) {
            $slot = \App\Models\Slot::find($validated['slot_id']);

            if (! $slot || $slot->status !== 'available') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'slot_id' => app()->getLocale() === 'ar'
                        ? 'عذراً، هذا الموعد لم يعد متاحاً'
                        : 'Sorry, this slot is no longer available',
                ]);
            }

            // Check if slot is in the future
            $slotDateTime = \Carbon\Carbon::parse($slot->date->format('Y-m-d').' '.$slot->start_time);
            if ($slotDateTime->isPast()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'slot_id' => app()->getLocale() === 'ar'
                        ? 'لا يمكن حجز موعد في الماضي'
                        : 'Cannot book a slot in the past',
                ]);
            }
        }

        return $validated;
    }
}
