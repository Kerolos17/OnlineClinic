@extends('layouts.app')

@section('title', __('messages.specializations'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-br from-medical-600 to-medical-700 py-20 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl animate-pulse-soft"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-white rounded-full blur-3xl animate-pulse-soft" style="animation-delay: 1s;"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white">
            <!-- Icon -->
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-6 animate-float">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">
                {{ __('messages.specializations') }}
            </h1>
            <p class="text-xl md:text-2xl text-medical-100 max-w-3xl mx-auto">
                {{ app()->getLocale() == 'ar' ? 'اختر التخصص الطبي المناسب لاحتياجاتك الصحية' : 'Choose the medical specialization that fits your healthcare needs' }}
            </p>
            
            <!-- Stats -->
            <div class="flex flex-wrap items-center justify-center gap-8 mt-8">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $specializations->count() }}</div>
                    <div class="text-medical-200 text-sm">{{ app()->getLocale() == 'ar' ? 'تخصص' : 'Specializations' }}</div>
                </div>
                <div class="w-px h-12 bg-white/20"></div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $specializations->sum(fn($s) => $s->doctors->count()) }}</div>
                    <div class="text-medical-200 text-sm">{{ app()->getLocale() == 'ar' ? 'طبيب' : 'Doctors' }}</div>
                </div>
                <div class="w-px h-12 bg-white/20"></div>
                <div class="text-center">
                    <div class="text-3xl font-bold">24/7</div>
                    <div class="text-medical-200 text-sm">{{ app()->getLocale() == 'ar' ? 'متاح' : 'Available' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Section Header -->
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mb-4">
            {{ app()->getLocale() == 'ar' ? 'استكشف التخصصات الطبية' : 'Explore Medical Specializations' }}
        </h2>
        <p class="text-lg text-medical-600 max-w-2xl mx-auto">
            {{ app()->getLocale() == 'ar' ? 'نوفر لك أفضل الأطباء في مختلف التخصصات الطبية' : 'We provide you with the best doctors across various medical specializations' }}
        </p>
    </div>

    <!-- Specializations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($specializations as $specialization)
        <a href="{{ route('doctors.index', ['specialization' => $specialization->id]) }}" 
           class="card p-8 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-100 to-transparent rounded-bl-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            
            <!-- Icon -->
            <div class="relative w-20 h-20 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-5xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                {{ $specialization->icon }}
            </div>
            
            <!-- Name -->
            <h3 class="relative text-2xl font-bold text-medical-900 mb-3 group-hover:text-medical-600 transition-colors">
                {{ app()->getLocale() == 'ar' ? $specialization->name_ar : $specialization->name_en }}
            </h3>
            
            <!-- Description -->
            @if(app()->getLocale() == 'ar' ? $specialization->description_ar : $specialization->description_en)
            <p class="relative text-medical-600 mb-6 line-clamp-3 leading-relaxed">
                {{ app()->getLocale() == 'ar' ? $specialization->description_ar : $specialization->description_en }}
            </p>
            @endif
            
            <!-- Footer -->
            <div class="relative flex items-center justify-between pt-4 border-t border-medical-100">
                <div class="flex items-center gap-2 text-medical-700">
                    <div class="w-8 h-8 bg-medical-100 rounded-lg flex items-center justify-center group-hover:bg-medical-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-lg">{{ $specialization->doctors->count() }}</span>
                        <span class="text-sm">{{ app()->getLocale() == 'ar' ? 'طبيب' : 'doctors' }}</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-medical-600 font-medium group-hover:text-medical-700 transition-colors">
                    <span class="text-sm">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-3">
            <div class="card p-16 text-center">
                <div class="w-32 h-32 bg-gradient-to-br from-medical-100 to-medical-200 rounded-full flex items-center justify-center text-7xl mx-auto mb-8 animate-float">
                    🏥
                </div>
                <h3 class="text-3xl font-bold text-medical-900 mb-3">
                    {{ app()->getLocale() == 'ar' ? 'لا توجد تخصصات' : 'No Specializations Available' }}
                </h3>
                <p class="text-lg text-medical-600 mb-8 max-w-md mx-auto">
                    {{ app()->getLocale() == 'ar' ? 'لا توجد تخصصات طبية متاحة حالياً. يرجى المحاولة لاحقاً' : 'There are no medical specializations available at the moment. Please try again later' }}
                </p>
                <a href="{{ route('home') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>{{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Features Section -->
<div class="bg-medical-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'أطباء معتمدون' : 'Certified Doctors' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar' ? 'جميع أطبائنا معتمدون ومؤهلون' : 'All our doctors are certified and qualified' }}
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'متاح على مدار الساعة' : '24/7 Availability' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar' ? 'احجز استشارتك في أي وقت' : 'Book your consultation anytime' }}
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'استشارات عبر الفيديو' : 'Video Consultations' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar' ? 'استشارات آمنة عبر الإنترنت' : 'Secure online consultations' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-br from-medical-600 to-medical-700 py-20 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            {{ app()->getLocale() == 'ar' ? 'لم تجد التخصص المناسب؟' : 'Didn\'t Find the Right Specialization?' }}
        </h2>
        <p class="text-xl md:text-2xl text-medical-100 mb-10 max-w-2xl mx-auto">
            {{ app()->getLocale() == 'ar' ? 'تصفح جميع الأطباء المتاحين لدينا واختر الأنسب لك' : 'Browse all our available doctors and choose the best fit for you' }}
        </p>
        
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('doctors.index') }}" class="bg-white text-medical-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-medical-50 transition-all duration-300 inline-flex items-center gap-3 shadow-xl hover:shadow-2xl hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ app()->getLocale() == 'ar' ? 'عرض جميع الأطباء' : 'View All Doctors' }}</span>
            </a>
            
            <a href="{{ route('home') }}" class="bg-white/10 backdrop-blur-sm text-white border-2 border-white/30 px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/20 transition-all duration-300 inline-flex items-center gap-3">
                <svg class="w-6 h-6 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
