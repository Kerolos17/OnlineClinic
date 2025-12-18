@extends('layouts.app')

@section('title', __('messages.booking_confirmed'))

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="card p-8 md:p-12 text-center">
            <!-- Success Animation -->
            <div class="relative mb-8 animate-bounce-in">
                <div class="success-checkmark mx-auto">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                        <div class="icon-fix"></div>
                    </div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center -z-10">
                    <div class="w-40 h-40 bg-accent-200 rounded-full blur-2xl opacity-50 animate-pulse-ring"></div>
                </div>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-medical-900 mb-4">
                {{ __('messages.booking_confirmed') }}
            </h1>

            <p class="text-lg text-medical-600 mb-8 max-w-md mx-auto">
                {{ app()->getLocale() == 'ar' ? 'تم تأكيد حجزك بنجاح! سنرسل لك رابط الاجتماع قبل الموعد.' : 'Your booking has been confirmed successfully! We will send you the meeting link before the appointment.' }}
            </p>

            <!-- Booking Details -->
            <div
                class="bg-gradient-to-br from-medical-50 to-accent-50 p-6 rounded-2xl mb-8 text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} border border-medical-100">
                <h2 class="font-bold text-medical-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل الحجز' : 'Booking Details' }}
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="text-medical-600 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Booking ID' }}
                        </span>
                        <span class="font-bold text-medical-900">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="text-medical-600 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'الطبيب' : 'Doctor' }}
                        </span>
                        <span
                            class="font-bold text-medical-900">{{ app()->getLocale() == 'ar' ? $booking->doctor->user->name_ar : $booking->doctor->user->name_en }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="text-medical-600 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'التاريخ والوقت' : 'Date & Time' }}
                        </span>
                        <span
                            class="font-bold text-medical-900">{{ $booking->appointment_at->format('M d, Y - H:i') }}</span>
                    </div>

                    <div
                        class="flex items-center justify-between p-3 bg-gradient-to-r from-accent-500 to-accent-600 rounded-xl text-white">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'المبلغ المدفوع' : 'Amount Paid' }}
                        </span>
                        <span class="font-bold text-2xl">${{ $booking->amount }}</span>
                    </div>
                </div>
            </div>

            <!-- Email Confirmation Notice -->
            <div class="bg-medical-50 border border-medical-200 p-4 rounded-xl mb-8 flex items-start gap-3">
                <svg class="w-6 h-6 text-medical-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div class="text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">
                    <p class="text-sm font-medium text-medical-900 mb-1">
                        {{ app()->getLocale() == 'ar' ? 'تحقق من بريدك الإلكتروني' : 'Check Your Email' }}
                    </p>
                    <p class="text-sm text-medical-600">
                        {{ app()->getLocale() == 'ar' ? 'تم إرسال تأكيد الحجز ورابط الاجتماع إلى بريدك الإلكتروني' : 'Booking confirmation and meeting link have been sent to your email' }}
                    </p>
                </div>
            </div>

            <!-- What's Next -->
            <div class="mb-8 text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">
                <h3 class="font-bold text-medical-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'ماذا بعد؟' : 'What\'s Next?' }}
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-medical-100">
                        <div
                            class="w-8 h-8 bg-medical-100 rounded-lg flex items-center justify-center flex-shrink-0 text-medical-600 font-bold">
                            1</div>
                        <div>
                            <p class="font-medium text-medical-900">
                                {{ app()->getLocale() == 'ar' ? 'تحقق من بريدك الإلكتروني' : 'Check your email' }}</p>
                            <p class="text-sm text-medical-600">
                                {{ app()->getLocale() == 'ar' ? 'ستجد تفاصيل الحجز ورابط الاجتماع' : 'You\'ll find booking details and meeting link' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-medical-100">
                        <div
                            class="w-8 h-8 bg-medical-100 rounded-lg flex items-center justify-center flex-shrink-0 text-medical-600 font-bold">
                            2</div>
                        <div>
                            <p class="font-medium text-medical-900">
                                {{ app()->getLocale() == 'ar' ? 'جهز نفسك للموعد' : 'Prepare for your appointment' }}</p>
                            <p class="text-sm text-medical-600">
                                {{ app()->getLocale() == 'ar' ? 'اكتب أي أسئلة أو أعراض تريد مناقشتها' : 'Write down any questions or symptoms to discuss' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-medical-100">
                        <div
                            class="w-8 h-8 bg-medical-100 rounded-lg flex items-center justify-center flex-shrink-0 text-medical-600 font-bold">
                            3</div>
                        <div>
                            <p class="font-medium text-medical-900">
                                {{ app()->getLocale() == 'ar' ? 'انضم للاجتماع' : 'Join the meeting' }}</p>
                            <p class="text-sm text-medical-600">
                                {{ app()->getLocale() == 'ar' ? 'استخدم رابط Zoom قبل 5 دقائق من الموعد' : 'Use the Zoom link 5 minutes before appointment' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="btn-primary">
                    <svg class="w-5 h-5 inline {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
                </a>
                <a href="{{ route('doctors.index') }}" class="btn-secondary">
                    {{ app()->getLocale() == 'ar' ? 'حجز موعد آخر' : 'Book Another Appointment' }}
                </a>
            </div>
        </div>
    </div>
@endsection
