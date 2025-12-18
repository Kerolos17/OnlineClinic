@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? $specialization->name_ar : $specialization->name_en)

@section('content')
<!-- Specialization Header -->
<div class="bg-gradient-to-br from-medical-600 to-medical-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-6 text-white">
            <!-- Icon -->
            <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-6xl flex-shrink-0">
                {{ $specialization->icon }}
            </div>

            <!-- Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <a href="{{ route('specializations.index') }}" class="text-medical-100 hover:text-white transition-colors">
                        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <span class="text-medical-200">{{ __('messages.specializations') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    {{ app()->getLocale() == 'ar' ? $specialization->name_ar : $specialization->name_en }}
                </h1>

                @if(app()->getLocale() == 'ar' ? $specialization->description_ar : $specialization->description_en)
                <p class="text-xl text-medical-100 max-w-3xl">
                    {{ app()->getLocale() == 'ar' ? $specialization->description_ar : $specialization->description_en }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="card p-6 text-center">
            <div class="text-4xl font-bold text-medical-600 mb-2">{{ $specialization->doctors->count() }}</div>
            <div class="text-medical-700 font-medium">
                {{ app()->getLocale() == 'ar' ? 'طبيب متاح' : 'Available Doctors' }}
            </div>
        </div>

        <div class="card p-6 text-center">
            <div class="text-4xl font-bold text-accent-600 mb-2">
                {{ $specialization->doctors->where('is_active', true)->count() }}
            </div>
            <div class="text-medical-700 font-medium">
                {{ app()->getLocale() == 'ar' ? 'طبيب نشط' : 'Active Now' }}
            </div>
        </div>

        <div class="card p-6 text-center">
            <div class="text-4xl font-bold text-medical-600 mb-2">
                @php
                    $avgPrice = $specialization->doctors->avg('consultation_price');
                @endphp
                ${{ number_format($avgPrice ?? 0, 0) }}
            </div>
            <div class="text-medical-700 font-medium">
                {{ app()->getLocale() == 'ar' ? 'متوسط السعر' : 'Average Price' }}
            </div>
        </div>
    </div>

    <!-- Doctors Section -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-medical-900 mb-6">
            {{ app()->getLocale() == 'ar' ? 'الأطباء المتخصصون' : 'Specialist Doctors' }}
        </h2>
    </div>

    <!-- Doctors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($specialization->doctors as $doctor)
        <a href="{{ route('doctors.show', $doctor) }}" class="card p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <!-- Doctor Avatar -->
            <div class="flex items-start gap-4 mb-4">
                @php
                    $doctorName = app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en;
                    $initials = collect(explode(' ', $doctorName))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->join('');
                @endphp
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-medical-400 to-medical-600 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                    {{ $initials }}
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold text-medical-900 mb-1 group-hover:text-medical-600 transition-colors truncate">
                        {{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}
                    </h3>

                    <!-- Availability Badge -->
                    @if($doctor->is_active)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-accent-100 text-accent-700 rounded-full text-xs font-medium">
                        <span class="w-2 h-2 bg-accent-500 rounded-full animate-pulse"></span>
                        {{ app()->getLocale() == 'ar' ? 'متاح الآن' : 'Available' }}
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-medical-100 text-medical-600 rounded-full text-xs font-medium">
                        {{ app()->getLocale() == 'ar' ? 'غير متاح' : 'Unavailable' }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Bio -->
            @if($doctor->bio)
            <p class="text-medical-600 text-sm mb-4 line-clamp-2">
                {{ $doctor->bio[app()->getLocale()] ?? $doctor->bio['en'] ?? '' }}
            </p>
            @endif

            <!-- Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-medical-100">
                <div class="text-medical-900">
                    <span class="text-2xl font-bold">${{ number_format($doctor->consultation_price, 0) }}</span>
                    <span class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? '/استشارة' : '/session' }}</span>
                </div>

                <svg class="w-5 h-5 text-medical-400 group-hover:text-medical-600 group-hover:translate-x-1 transition-all {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </div>
        </a>
        @empty
        <div class="col-span-3">
            <div class="card p-12 text-center">
                <div class="w-24 h-24 bg-medical-100 rounded-full flex items-center justify-center text-6xl mx-auto mb-6">
                    👨‍⚕️
                </div>
                <h3 class="text-2xl font-bold text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'لا يوجد أطباء' : 'No Doctors Available' }}
                </h3>
                <p class="text-medical-600 mb-6">
                    {{ app()->getLocale() == 'ar' ? 'لا يوجد أطباء متاحون في هذا التخصص حالياً' : 'There are no doctors available in this specialization at the moment' }}
                </p>
                <a href="{{ route('specializations.index') }}" class="btn-secondary inline-block">
                    {{ app()->getLocale() == 'ar' ? 'تصفح التخصصات' : 'Browse Specializations' }}
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-br from-medical-50 to-accent-50 py-16 mt-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mb-4">
            {{ app()->getLocale() == 'ar' ? 'جاهز لحجز استشارتك؟' : 'Ready to Book Your Consultation?' }}
        </h2>
        <p class="text-xl text-medical-600 mb-8">
            {{ app()->getLocale() == 'ar' ? 'اختر طبيبك المفضل وابدأ رحلتك الصحية اليوم' : 'Choose your preferred doctor and start your health journey today' }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('doctors.index', ['specialization' => $specialization->id]) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ app()->getLocale() == 'ar' ? 'احجز الآن' : 'Book Now' }}</span>
            </a>
            <a href="{{ route('specializations.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <span>{{ app()->getLocale() == 'ar' ? 'تخصصات أخرى' : 'Other Specializations' }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
