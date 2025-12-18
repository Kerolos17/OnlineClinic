@extends('layouts.app')

@section('title', __('messages.booking_form'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Progress Steps -->
        <div class="mb-12">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center text-medical-600">
                        <div
                            class="w-10 h-10 bg-medical-600 rounded-full flex items-center justify-center text-white font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }} font-medium">{{ app()->getLocale() == 'ar' ? 'اختيار الطبيب' : 'Select Doctor' }}</span>
                    </div>
                    <div class="w-16 h-0.5 bg-medical-600 {{ app()->getLocale() == 'ar' ? 'mr-4' : 'mx-4' }}"></div>
                    <div class="flex items-center text-medical-600">
                        <div
                            class="w-10 h-10 bg-medical-600 rounded-full flex items-center justify-center text-white font-bold">
                            2</div>
                        <span
                            class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }} font-medium">{{ app()->getLocale() == 'ar' ? 'معلومات المريض' : 'Patient Info' }}</span>
                    </div>
                    <div class="w-16 h-0.5 bg-medical-200 {{ app()->getLocale() == 'ar' ? 'mr-4' : 'mx-4' }}"></div>
                    <div class="flex items-center text-medical-400">
                        <div
                            class="w-10 h-10 bg-medical-200 rounded-full flex items-center justify-center text-medical-600 font-bold">
                            3</div>
                        <span
                            class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }} font-medium">{{ app()->getLocale() == 'ar' ? 'الدفع' : 'Payment' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-8">
            <h1 class="text-3xl font-bold text-medical-900 mb-2">{{ __('messages.booking_form') }}</h1>
            <p class="text-medical-600 mb-8">
                {{ app()->getLocale() == 'ar' ? 'أدخل معلوماتك لإكمال الحجز' : 'Enter your information to complete the booking' }}
            </p>

            <!-- Booking Summary -->
            <div class="bg-gradient-to-br from-medical-50 to-accent-50 p-6 rounded-2xl mb-8 border border-medical-100">
                <h2 class="font-bold text-medical-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'ملخص الحجز' : 'Booking Summary' }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-medical-400 to-medical-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-medical-600">{{ app()->getLocale() == 'ar' ? 'الطبيب' : 'Doctor' }}
                            </div>
                            <div class="font-bold text-medical-900">
                                {{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}</div>
                            <div class="text-xs text-medical-600">
                                {{ $doctor->specialization->name[app()->getLocale()] ?? ($doctor->specialization->name['en'] ?? '') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-accent-400 to-accent-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-medical-600">{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}
                            </div>
                            <div class="font-bold text-medical-900">{{ $slot->date->format('M d, Y') }}</div>
                            <div class="text-xs text-medical-600">{{ $slot->start_time }} - {{ $slot->end_time }}</div>
                        </div>
                    </div>

                    <div
                        class="md:col-span-2 flex items-center justify-between p-4 bg-gradient-to-r from-medical-600 to-medical-500 rounded-xl text-white">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">{{ __('messages.consultation_price') }}</span>
                        </div>
                        <span class="text-3xl font-bold">${{ $doctor->consultation_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form method="POST" action="{{ route('booking.store') }}">
                @csrf
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <input type="hidden" name="slot_id" value="{{ $slot->id }}">

                <div class="space-y-6">
                    <div>
                        <label class="form-label">
                            <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('messages.patient_name') }} *
                        </label>
                        <input type="text" name="patient_name" required
                            class="form-input @error('patient_name') border-red-500 @enderror"
                            placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}"
                            value="{{ old('patient_name') }}">
                        @error('patient_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ __('messages.patient_email') }} *
                        </label>
                        <input type="email" name="patient_email" required
                            class="form-input @error('patient_email') border-red-500 @enderror"
                            placeholder="{{ app()->getLocale() == 'ar' ? 'example@email.com' : 'example@email.com' }}"
                            value="{{ old('patient_email') }}">
                        @error('patient_email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ __('messages.patient_phone') }} *
                        </label>
                        <input type="tel" name="patient_phone" required
                            class="form-input @error('patient_phone') border-red-500 @enderror"
                            placeholder="{{ app()->getLocale() == 'ar' ? '+966 XX XXX XXXX' : '+1 (555) 123-4567' }}"
                            value="{{ old('patient_phone') }}">
                        @error('patient_phone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            {{ __('messages.patient_notes') }}
                            <span
                                class="text-medical-500 font-normal">({{ app()->getLocale() == 'ar' ? 'اختياري' : 'Optional' }})</span>
                        </label>
                        <textarea name="patient_notes" id="patient_notes" rows="4" maxlength="1000" class="form-input"
                            placeholder="{{ app()->getLocale() == 'ar' ? 'أخبرنا عن حالتك الصحية أو أي ملاحظات إضافية...' : 'Tell us about your health condition or any additional notes...' }}">{{ old('patient_notes') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-xs text-medical-500">
                                {{ app()->getLocale() == 'ar' ? 'ساعدنا على فهم حالتك بشكل أفضل' : 'Help us understand your condition better' }}
                            </p>
                            <p class="text-xs text-medical-500">
                                <span id="notes-counter">0</span> / 1000
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ route('doctors.show', $doctor->id) }}" class="btn-secondary text-center">
                            <svg class="w-5 h-5 inline {{ app()->getLocale() == 'ar' ? 'ml-2 rotate-180' : 'mr-2' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'رجوع' : 'Back' }}
                        </a>
                        <button type="submit" class="btn-primary flex-1">
                            {{ __('messages.proceed_to_payment') }}
                            <svg class="w-5 h-5 inline {{ app()->getLocale() == 'ar' ? 'mr-2 rotate-180' : 'ml-2' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter for notes
            const notesTextarea = document.getElementById('patient_notes');
            const notesCounter = document.getElementById('notes-counter');

            if (notesTextarea && notesCounter) {
                // Initialize counter
                notesCounter.textContent = notesTextarea.value.length;

                notesTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    notesCounter.textContent = length;

                    // Change color based on length
                    if (length > 900) {
                        notesCounter.classList.add('text-red-600', 'font-bold');
                        notesCounter.classList.remove('text-medical-500');
                    } else if (length > 700) {
                        notesCounter.classList.add('text-yellow-600', 'font-semibold');
                        notesCounter.classList.remove('text-medical-500', 'text-red-600');
                    } else {
                        notesCounter.classList.remove('text-red-600', 'text-yellow-600', 'font-bold',
                            'font-semibold');
                        notesCounter.classList.add('text-medical-500');
                    }
                });
            }

            // Add smooth scroll to first error
            const firstError = document.querySelector('.form-error, .validation-error');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
@endsection
