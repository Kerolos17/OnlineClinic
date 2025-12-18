@extends('layouts.app')

@section('title', __('messages.welcome'))

@section('content')
<!-- Hero Section -->
<div class="hero-gradient relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="text-white {{ app()->getLocale() == 'ar' ? 'lg:text-right' : 'lg:text-left' }} text-center lg:text-left">
                <div class="inline-block mb-4">
                    <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium">
                        {{ app()->getLocale() == 'ar' ? '🏥 رعاية صحية موثوقة' : '🏥 Trusted Healthcare' }}
                    </span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    {{ app()->getLocale() == 'ar' ? 'صحتك تبدأ هنا' : 'Your Health Starts Here' }}
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-medical-100 leading-relaxed">
                    {{ app()->getLocale() == 'ar' ? 'احجز استشارتك الطبية أونلاين مع أفضل الأطباء بكل سهولة وأمان' : 'Book your online medical consultation with top doctors easily and securely' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 {{ app()->getLocale() == 'ar' ? 'lg:justify-end' : 'lg:justify-start' }} justify-center">
                    <a href="{{ route('doctors.index') }}"
                       class="bg-white text-medical-600 px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 inline-flex items-center justify-center gap-2">
                        <span>{{ __('messages.book_now') }}</span>
                        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="{{ route('specializations.index') }}"
                       class="bg-white/10 backdrop-blur-sm text-white border-2 border-white/30 px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/20 transition-all duration-300 inline-flex items-center justify-center gap-2">
                        <span>{{ app()->getLocale() == 'ar' ? 'التخصصات' : 'Specializations' }}</span>
                    </a>
                </div>
            </div>

            <!-- Hero Image/Illustration -->
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-medical-400/20 to-accent-400/20 rounded-3xl blur-3xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="stat-card text-center animate-float">
                                <div class="text-4xl mb-2">👨‍⚕️</div>
                                <div class="text-2xl font-bold text-medical-700">{{ $stats['total_doctors'] }}{{ $stats['total_doctors'] >= 100 ? '+' : '' }}</div>
                                <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'طبيب' : 'Doctors' }}</div>
                            </div>
                            <div class="stat-card text-center animate-float" style="animation-delay: 0.2s;">
                                <div class="text-4xl mb-2">⭐</div>
                                <div class="text-2xl font-bold text-medical-700">{{ $stats['average_rating'] ?? '5.0' }}</div>
                                <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'التقييم' : 'Rating' }}</div>
                            </div>
                            <div class="stat-card text-center animate-float" style="animation-delay: 0.4s;">
                                <div class="text-4xl mb-2">📅</div>
                                <div class="text-2xl font-bold text-medical-700">
                                    @if($stats['total_bookings'] >= 1000)
                                        {{ number_format($stats['total_bookings'] / 1000, 1) }}K+
                                    @else
                                        {{ $stats['total_bookings'] }}{{ $stats['total_bookings'] >= 100 ? '+' : '' }}
                                    @endif
                                </div>
                                <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'حجز' : 'Bookings' }}</div>
                            </div>
                            <div class="stat-card text-center animate-float" style="animation-delay: 0.6s;">
                                <div class="text-4xl mb-2">😊</div>
                                <div class="text-2xl font-bold text-medical-700">{{ $stats['satisfaction_rate'] }}%</div>
                                <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'رضا' : 'Satisfaction' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Specializations Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-12">
        <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">{{ app()->getLocale() == 'ar' ? 'التخصصات الطبية' : 'Medical Specializations' }}</span>
        <h2 class="text-4xl font-bold text-medical-900 mt-2 mb-4">{{ __('messages.specializations') }}</h2>
        <p class="text-medical-600 text-lg max-w-2xl mx-auto">
            {{ app()->getLocale() == 'ar' ? 'اختر التخصص المناسب لحالتك الصحية' : 'Choose the right specialization for your health needs' }}
        </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
        @foreach($specializations as $spec)
        <a href="{{ route('doctors.index', ['specialization' => $spec->id]) }}" class="spec-card group">
            <div class="spec-icon">{{ $spec->icon }}</div>
            <h3 class="font-bold text-medical-800 group-hover:text-medical-600 transition-colors">{{ app()->getLocale() == 'ar' ? $spec->name_ar : $spec->name_en }}</h3>
        </a>
        @endforeach
    </div>
</div>

<!-- Top Doctors Section -->
<div class="bg-gradient-to-br from-medical-50 to-accent-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">{{ app()->getLocale() == 'ar' ? 'الأطباء المميزون' : 'Featured Doctors' }}</span>
            <h2 class="text-4xl font-bold text-medical-900 mt-2 mb-4">
                {{ app()->getLocale() == 'ar' ? 'أفضل الأطباء' : 'Top Rated Doctors' }}
            </h2>
            <p class="text-medical-600 text-lg max-w-2xl mx-auto">
                {{ app()->getLocale() == 'ar' ? 'تواصل مع أفضل الأطباء ذوي الخبرة والتقييمات العالية' : 'Connect with experienced doctors with high ratings' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topDoctors as $doctor)
            <div class="doctor-card group">
                <div class="flex items-start gap-6 mb-6">
                    <div class="doctor-avatar group-hover:scale-105 transition-transform duration-300 w-24 h-24 flex-shrink-0">
                        @if($doctor->image)
                            <img src="{{ asset('storage/' . $doctor->image) }}" alt="{{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}" class="w-full h-full rounded-2xl object-cover shadow-lg">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-medical-100 to-medical-200 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-12 h-12 text-medical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-xl text-medical-900 mb-2 truncate">{{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}</h3>
                        <p class="text-medical-600 text-base mb-3 font-medium">{{ app()->getLocale() == 'ar' ? $doctor->specialization->name_ar : $doctor->specialization->name_en }}</p>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 bg-yellow-50 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                                <span class="font-bold text-sm text-yellow-700">{{ $doctor->rating }}</span>
                            </div>
                            <span class="text-medical-600 text-sm font-medium">({{ $doctor->total_reviews }} {{ app()->getLocale() == 'ar' ? 'تقييم' : 'reviews' }})</span>
                    </div>
            </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-medical-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ __('messages.experience_years') }}
                        </span>
                        <span class="font-semibold text-medical-800">{{ $doctor->experience_years }} {{ app()->getLocale() == 'ar' ? 'سنة' : 'years' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-medical-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            {{ __('messages.languages') }}
                        </span>
                        <span class="font-semibold text-medical-800">{{ implode(', ', $doctor->languages) }}</span>
                    </div>
                </div>

                <div class="border-t border-medical-100 pt-4 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-medical-600 mb-1">{{ __('messages.consultation_price') }}</div>
                        <div class="text-2xl font-bold text-medical-600">${{ $doctor->consultation_price }}</div>
                    </div>
                    <a href="{{ route('doctors.show', $doctor->id) }}" class="btn-primary">
                        {{ __('messages.book_now') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('doctors.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <span>{{ app()->getLocale() == 'ar' ? 'عرض جميع الأطباء' : 'View All Doctors' }}</span>
                <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-16">
        <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">{{ app()->getLocale() == 'ar' ? 'عملية بسيطة' : 'Simple Process' }}</span>
        <h2 class="text-4xl font-bold text-medical-900 mt-2 mb-4">
            {{ app()->getLocale() == 'ar' ? 'كيف يعمل الموقع؟' : 'How It Works?' }}
        </h2>
        <p class="text-medical-600 text-lg max-w-2xl mx-auto">
            {{ app()->getLocale() == 'ar' ? 'احجز استشارتك الطبية في 4 خطوات سهلة' : 'Book your medical consultation in 4 easy steps' }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="text-center group">
            <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    1
                </div>
                <div class="hidden lg:block absolute top-10 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-full' }} w-full h-0.5 bg-gradient-to-{{ app()->getLocale() == 'ar' ? 'l' : 'r' }} from-medical-300 to-transparent"></div>
            </div>
            <div class="w-16 h-16 bg-medical-100 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                🔍
            </div>
            <h3 class="font-bold text-xl text-medical-900 mb-2">{{ app()->getLocale() == 'ar' ? 'اختر الطبيب' : 'Choose Doctor' }}</h3>
            <p class="text-medical-600">{{ app()->getLocale() == 'ar' ? 'تصفح الأطباء حسب التخصص والتقييمات' : 'Browse doctors by specialization and ratings' }}</p>
        </div>

        <div class="text-center group">
            <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    2
                </div>
                <div class="hidden lg:block absolute top-10 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-full' }} w-full h-0.5 bg-gradient-to-{{ app()->getLocale() == 'ar' ? 'l' : 'r' }} from-medical-300 to-transparent"></div>
            </div>
            <div class="w-16 h-16 bg-medical-100 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                📅
            </div>
            <h3 class="font-bold text-xl text-medical-900 mb-2">{{ app()->getLocale() == 'ar' ? 'احجز موعد' : 'Book Appointment' }}</h3>
            <p class="text-medical-600">{{ app()->getLocale() == 'ar' ? 'اختر التاريخ والوقت المناسب لك' : 'Select your preferred date and time' }}</p>
        </div>

        <div class="text-center group">
            <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    3
                </div>
                <div class="hidden lg:block absolute top-10 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-full' }} w-full h-0.5 bg-gradient-to-{{ app()->getLocale() == 'ar' ? 'l' : 'r' }} from-medical-300 to-transparent"></div>
            </div>
            <div class="w-16 h-16 bg-medical-100 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                💳
            </div>
            <h3 class="font-bold text-xl text-medical-900 mb-2">{{ app()->getLocale() == 'ar' ? 'ادفع بأمان' : 'Pay Securely' }}</h3>
            <p class="text-medical-600">{{ app()->getLocale() == 'ar' ? 'ادفع عبر الإنترنت بأمان تام' : 'Pay online with complete security' }}</p>
        </div>

        <div class="text-center group">
            <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    4
                </div>
            </div>
            <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                🎥
            </div>
            <h3 class="font-bold text-xl text-medical-900 mb-2">{{ app()->getLocale() == 'ar' ? 'ابدأ الجلسة' : 'Start Session' }}</h3>
            <p class="text-medical-600">{{ app()->getLocale() == 'ar' ? 'استلم رابط Zoom وابدأ الاستشارة' : 'Receive Zoom link and start consultation' }}</p>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-br from-medical-600 to-medical-700 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            {{ app()->getLocale() == 'ar' ? 'جاهز لبدء رحلتك الصحية؟' : 'Ready to Start Your Health Journey?' }}
        </h2>
        <p class="text-xl text-medical-100 mb-8">
            {{ app()->getLocale() == 'ar' ? 'احجز استشارتك الأولى اليوم واحصل على رعاية طبية متميزة' : 'Book your first consultation today and get exceptional medical care' }}
        </p>
        <a href="{{ route('doctors.index') }}" class="bg-white text-medical-600 px-10 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 inline-flex items-center gap-2">
            <span>{{ __('messages.book_now') }}</span>
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
