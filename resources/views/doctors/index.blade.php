@extends('layouts.app')

@section('title', __('messages.doctors'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-br from-medical-600 to-medical-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('messages.doctors') }}</h1>
            <p class="text-xl text-medical-100 max-w-2xl mx-auto">
                {{ app()->getLocale() == 'ar' ? 'اعثر على الطبيب المناسب لاحتياجاتك الصحية' : 'Find the right doctor for your healthcare needs' }}
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Filters -->
    <div class="card p-6 mb-8">
        <form method="GET" action="{{ route('doctors.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="form-label">
                    <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('messages.search_doctors') }}
                </label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن طبيب...' : 'Search for a doctor...' }}"
                       class="form-input">
            </div>

            <div>
                <label for="specialization" class="form-label">
                    <svg class="w-4 h-4 inline {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ __('messages.specializations') }}
                </label>
                <select name="specialization" class="form-input">
                    <option value="">{{ __('messages.all_specializations') }}</option>
                    @foreach($allSpecializations as $spec)
                    <option value="{{ $spec->id }}" {{ request('specialization') == $spec->id ? 'selected' : '' }}>
                        {{ $spec->icon }} {{ app()->getLocale() == 'ar' ? $spec->name_ar : $spec->name_en }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    <svg class="w-5 h-5 inline {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'بحث' : 'Search' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    @if(request('search') || request('specialization'))
    <div class="mb-6 flex items-center justify-between">
        <p class="text-medical-600">
            {{ app()->getLocale() == 'ar' ? 'تم العثور على' : 'Found' }}
            <span class="font-bold text-medical-800">{{ $doctors->total() }}</span>
            {{ app()->getLocale() == 'ar' ? 'طبيب' : 'doctors' }}
        </p>
        <a href="{{ route('doctors.index') }}" class="text-medical-600 hover:text-medical-700 font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ app()->getLocale() == 'ar' ? 'مسح الفلاتر' : 'Clear filters' }}
        </a>
    </div>
    @endif

    <!-- Doctors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($doctors as $doctor)
        <div class="doctor-card group">
            <div class="flex items-start gap-4 mb-6">
                <div class="doctor-avatar group-hover:scale-110 transition-transform duration-300">
                    @if($doctor->image)
                        <img src="{{ asset('storage/' . $doctor->image) }}" alt="{{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-xl text-medical-900 mb-1">
                        {{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}
                    </h3>
                    <p class="text-medical-600 text-sm mb-2 flex items-center gap-1">
                        <span class="text-lg">{{ $doctor->specialization->icon }}</span>
                        {{ app()->getLocale() == 'ar' ? $doctor->specialization->name_ar : $doctor->specialization->name_en }}
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1 text-yellow-500">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <span class="font-bold text-sm">{{ $doctor->rating }}</span>
                        </div>
                        <span class="text-medical-400">•</span>
                        <span class="text-medical-600 text-sm">({{ $doctor->total_reviews }})</span>
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

                               @if($doctor->bio)
                <p class="text-medical-600 text-sm line-clamp-2">
                    {{ $doctor->bio[app()->getLocale()] ?? $doctor->bio['en'] ?? '' }}
                </p>
                @endif
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
        @empty
        <div class="col-span-3">
            <div class="card p-12 text-center">
                <div class="w-24 h-24 bg-medical-100 rounded-full flex items-center justify-center text-6xl mx-auto mb-6">
                    🔍
                </div>
                <h3 class="text-2xl font-bold text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'لا يوجد أطباء' : 'No Doctors Found' }}
                </h3>
                <p class="text-medical-600 mb-6">
                    {{ app()->getLocale() == 'ar' ? 'لم نتمكن من العثور على أطباء يطابقون بحثك' : 'We couldn\'t find any doctors matching your search' }}
                </p>
                <a href="{{ route('doctors.index') }}" class="btn-secondary inline-block">
                    {{ app()->getLocale() == 'ar' ? 'عرض جميع الأطباء' : 'View All Doctors' }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($doctors->hasPages())
    <div class="mt-12">
        {{ $doctors->links() }}
    </div>
    @endif
</div>
@endsection
