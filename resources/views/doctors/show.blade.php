@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <a href="{{ route('doctors.index') }}"
            class="inline-flex items-center gap-2 text-medical-600 hover:text-medical-700 font-medium mb-6 group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ app()->getLocale() == 'ar' ? 'العودة للأطباء' : 'Back to Doctors' }}
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Doctor Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="card p-6 sticky top-24">
                    <!-- Doctor Avatar & Basic Info -->
                    <div class="text-center mb-6">
                        <div
                            class="w-32 h-32 mx-auto mb-4 rounded-3xl bg-gradient-to-br from-medical-400 to-medical-600 flex items-center justify-center text-white shadow-xl">
                            @if ($doctor->image)
                                <img src="{{ asset('storage/' . $doctor->image) }}"
                                    alt="{{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}"
                                    class="w-full h-full object-cover rounded-3xl">
                            @else
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-medical-900 mb-2">
                            {{ app()->getLocale() == 'ar' ? $doctor->user->name_ar : $doctor->user->name_en }}
                        </h1>
                        <p class="text-medical-600 flex items-center justify-center gap-2 mb-3">
                            <span class="text-2xl">{{ $doctor->specialization->icon }}</span>
                            <span>{{ app()->getLocale() == 'ar' ? $doctor->specialization->name_ar : $doctor->specialization->name_en }}</span>
                        </p>
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <div class="flex items-center gap-1 text-yellow-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($doctor->rating))
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="font-bold text-medical-900">{{ $doctor->rating }}</span>
                            <span class="text-medical-600">({{ $doctor->total_reviews }}
                                {{ app()->getLocale() == 'ar' ? 'تقييم' : 'reviews' }})</span>
                        </div>
                    </div>

                    <!-- Doctor Stats -->
                    <div class="space-y-4 border-t border-medical-100 pt-6">
                        <div class="flex items-center justify-between p-3 bg-medical-50 rounded-xl">
                            <span class="text-medical-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium">{{ __('messages.experience_years') }}</span>
                            </span>
                            <span class="font-bold text-medical-900">{{ $doctor->experience_years }}
                                {{ app()->getLocale() == 'ar' ? 'سنة' : 'years' }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-medical-50 rounded-xl">
                            <span class="text-medical-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                <span class="font-medium">{{ __('messages.languages') }}</span>
                            </span>
                            <span class="font-bold text-medical-900">{{ implode(', ', $doctor->languages) }}</span>
                        </div>

                        <div
                            class="p-4 bg-gradient-to-br from-medical-500 to-medical-600 rounded-xl text-white text-center">
                            <div class="text-sm mb-1 opacity-90">{{ __('messages.consultation_price') }}</div>
                            <div class="text-3xl font-bold">${{ $doctor->consultation_price }}</div>
                            <div class="text-xs opacity-75 mt-1">
                                {{ app()->getLocale() == 'ar' ? 'لكل جلسة' : 'per session' }}</div>
                        </div>
                    </div>

                    <!-- About Section -->
                    @if ($doctor->bio)
                        <div class="mt-6 border-t border-medical-100 pt-6">
                            <h3 class="font-bold text-medical-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ app()->getLocale() == 'ar' ? 'نبذة عن الطبيب' : 'About Doctor' }}
                            </h3>
                            <p class="text-medical-600 text-sm leading-relaxed">
                                {{ $doctor->bio[app()->getLocale()] ?? ($doctor->bio['en'] ?? '') }}
                            </p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Booking Section -->
            <div class="lg:col-span-2">
                <div class="card p-8">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-medical-900 mb-2">{{ __('messages.book_now') }}</h2>
                        <p class="text-medical-600">
                            {{ app()->getLocale() == 'ar' ? 'اختر التاريخ والوقت المناسب لك' : 'Select your preferred date and time' }}
                        </p>
                    </div>

                    <!-- Calendar -->
                    <div class="mb-10">
                        <h3 class="font-bold text-medical-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('messages.select_date') }}
                        </h3>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-3" id="calendar">
                            @for ($i = 0; $i < 14; $i++)
                                @php
                                    $date = now()->addDays($i);
                                    $dayName =
                                        app()->getLocale() == 'ar'
                                            ? ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'][
                                                $date->dayOfWeek
                                            ]
                                            : $date->format('D');
                                @endphp
                                <button type="button" onclick="selectDate('{{ $date->format('Y-m-d') }}')"
                                    class="date-btn {{ $i === 0 ? 'selected' : '' }}"
                                    data-date="{{ $date->format('Y-m-d') }}">
                                    <div class="text-xs text-medical-600 font-medium mb-1">{{ $dayName }}</div>
                                    <div class="text-2xl font-bold text-medical-900">{{ $date->format('d') }}</div>
                                    <div class="text-xs text-medical-500 mt-1">{{ $date->format('M') }}</div>
                                </button>
                            @endfor
                        </div>
                    </div>

                    <!-- Time Slots -->
                    <div id="slots-container" class="hidden">
                        <h3 class="font-bold text-medical-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('messages.available_slots') }}
                        </h3>
                        <div id="slots-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <!-- Slots will be loaded here via AJAX -->
                        </div>
                    </div>

                    <div id="loading-slots" class="hidden text-center py-12">
                        <div
                            class="inline-block w-12 h-12 border-4 border-medical-200 border-t-medical-600 rounded-full animate-spin mb-4">
                        </div>
                        <p class="text-medical-600">
                            {{ app()->getLocale() == 'ar' ? 'جاري التحميل...' : 'Loading slots...' }}</p>
                    </div>

                    <div id="no-slots" class="hidden text-center py-12">
                        <div
                            class="w-20 h-20 bg-medical-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                            📅
                        </div>
                        <h4 class="text-xl font-bold text-medical-900 mb-2">{{ __('messages.no_slots_available') }}</h4>
                        <p class="text-medical-600">
                            {{ app()->getLocale() == 'ar' ? 'لا توجد مواعيد متاحة في هذا اليوم' : 'No appointments available on this day' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12">
            <div class="card p-8">
                <h2 class="text-3xl font-bold text-medical-900 mb-6 flex items-center gap-3">
                    <svg class="w-8 h-8 text-medical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'التقييمات والمراجعات' : 'Reviews & Ratings' }}
                </h2>

                <!-- Reviews List -->
                @if ($doctor->approvedReviews->count() > 0)
                    <div class="space-y-6 mb-8">
                        @foreach ($doctor->approvedReviews->take(5) as $review)
                            <div class="border-b border-medical-100 pb-6 last:border-0">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-bold text-medical-900">{{ $review->patient_name }}</h4>
                                        <div class="flex items-center gap-1 mt-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }} fill-current"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <span
                                        class="text-sm text-medical-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if ($review->comment)
                                    <p class="text-medical-600">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 mb-8">
                        <div
                            class="w-16 h-16 bg-medical-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                            ⭐
                        </div>
                        <p class="text-medical-600">
                            {{ app()->getLocale() == 'ar' ? 'لا توجد تقييمات بعد' : 'No reviews yet' }}</p>
                    </div>
                @endif

                <!-- Add Review Form -->
                <div class="border-t border-medical-100 pt-8">
                    <h3 class="text-xl font-bold text-medical-900 mb-4">
                        {{ app()->getLocale() == 'ar' ? 'أضف تقييمك' : 'Add Your Review' }}
                    </h3>

                    @if (session('success'))
                        <div class="bg-accent-100 border border-accent-200 text-accent-800 px-4 py-3 rounded-xl mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $doctor) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">{{ app()->getLocale() == 'ar' ? 'الاسم' : 'Name' }}</label>
                                <input type="text" name="patient_name" required class="form-input"
                                    value="{{ old('patient_name') }}">
                                @error('patient_name')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                                <input type="email" name="patient_email" required class="form-input"
                                    value="{{ old('patient_email') }}">
                                @error('patient_email')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label">{{ app()->getLocale() == 'ar' ? 'التقييم' : 'Rating' }}</label>
                            <div class="flex items-center gap-2" id="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer star-label" data-rating="{{ $i }}">
                                        <input type="radio" name="rating" value="{{ $i }}" required
                                            class="hidden" {{ old('rating') == $i ? 'checked' : '' }}>
                                        <svg class="w-10 h-10 star-icon transition-all duration-200 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <p class="text-sm text-medical-600 mt-2" id="rating-text">
                                {{ app()->getLocale() == 'ar' ? 'اختر تقييمك' : 'Select your rating' }}</p>
                            @error('rating')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="form-label">{{ app()->getLocale() == 'ar' ? 'التعليق (اختياري)' : 'Comment (Optional)' }}</label>
                            <textarea name="comment" rows="4" class="form-input" maxlength="1000">{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary">
                            {{ app()->getLocale() == 'ar' ? 'إرسال التقييم' : 'Submit Review' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedDate = '{{ now()->format('Y-m-d') }}';
        let selectedSlot = null;
        let selectedRating = {{ old('rating') ?? 0 }};

        // Load slots for today on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSlots(selectedDate);
            initRatingStars();
        });

        function selectDate(date) {
            selectedDate = date;

            // Update UI
            document.querySelectorAll('.date-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            document.querySelector(`[data-date="${date}"]`).classList.add('selected');

            // Load slots
            loadSlots(date);
        }

        function loadSlots(date) {
            const slotsGrid = document.getElementById('slots-grid');
            const slotsContainer = document.getElementById('slots-container');
            const noSlots = document.getElementById('no-slots');
            const loading = document.getElementById('loading-slots');
            const isArabic = document.documentElement.dir === 'rtl';

            // Show loading with animation
            slotsContainer.classList.add('hidden');
            noSlots.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.classList.add('animate-fade-in');

            fetch(`/api/doctors/{{ $doctor->id }}/slots?date=${date}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    loading.classList.add('hidden');
                    slotsGrid.innerHTML = '';

                    if (data.slots && data.slots.length > 0) {
                        slotsContainer.classList.remove('hidden');
                        slotsContainer.classList.add('animate-fade-in');

                        data.slots.forEach((slot, index) => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'slot-btn';
                            btn.style.animationDelay = `${index * 0.05}s`;
                            btn.classList.add('animate-scale-in');
                            btn.innerHTML = `
                        <div class="font-bold">${slot.start_time.substring(0, 5)}</div>
                        <div class="text-xs opacity-75 mt-1">${slot.end_time.substring(0, 5)}</div>
                    `;
                            btn.dataset.slotId = slot.id;
                            btn.onclick = () => selectSlot(slot.id, btn);
                            slotsGrid.appendChild(btn);
                        });
                    } else {
                        noSlots.classList.remove('hidden');
                        noSlots.classList.add('animate-fade-in');
                    }
                })
                .catch(error => {
                    loading.classList.add('hidden');
                    noSlots.classList.remove('hidden');
                    noSlots.classList.add('animate-fade-in');
                    console.error('Error loading slots:', error);

                    // Show error toast
                    if (window.Toast) {
                        window.Toast.show(
                            isArabic ? 'حدث خطأ في تحميل المواعيد' : 'Error loading slots',
                            'error'
                        );
                    }
                });
        }

        function selectSlot(slotId, button) {
            selectedSlot = slotId;
            const isArabic = document.documentElement.dir === 'rtl';

            // Update UI
            document.querySelectorAll('.slot-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            button.classList.add('selected');

            // Show loading on button
            if (window.LoadingManager) {
                window.LoadingManager.show(button, isArabic ? 'جاري التحميل...' : 'Loading...');
            }

            // Small delay for better UX
            setTimeout(() => {
                window.location.href = `/booking/create?doctor={{ $doctor->id }}&slot=${slotId}`;
            }, 300);
        }

        // Rating Stars Functionality
        function initRatingStars() {
            const starLabels = document.querySelectorAll('.star-label');
            const ratingText = document.getElementById('rating-text');
            const isArabic = '{{ app()->getLocale() }}' === 'ar';

            const ratingTexts = {
                en: ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'],
                ar: ['ضعيف', 'مقبول', 'جيد', 'جيد جداً', 'ممتاز']
            };

            // Initialize stars based on old value
            if (selectedRating > 0) {
                updateStars(selectedRating);
            }

            starLabels.forEach((label, index) => {
                const rating = index + 1;
                const starIcon = label.querySelector('.star-icon');
                const radioInput = label.querySelector('input[type="radio"]');

                // Click event
                label.addEventListener('click', function() {
                    selectedRating = rating;
                    radioInput.checked = true;
                    updateStars(rating);
                    updateRatingText(rating);
                });

                // Hover event
                label.addEventListener('mouseenter', function() {
                    updateStars(rating, true);
                    updateRatingText(rating);
                });

                // Mouse leave - restore selected rating
                label.addEventListener('mouseleave', function() {
                    if (selectedRating > 0) {
                        updateStars(selectedRating);
                        updateRatingText(selectedRating);
                    } else {
                        resetStars();
                        ratingText.textContent = isArabic ? 'اختر تقييمك' : 'Select your rating';
                    }
                });
            });

            function updateStars(rating, isHover = false) {
                starLabels.forEach((label, index) => {
                    const starIcon = label.querySelector('.star-icon');
                    if (index < rating) {
                        starIcon.classList.remove('text-gray-300');
                        starIcon.classList.add('text-yellow-500');
                        if (isHover) {
                            starIcon.classList.add('scale-110');
                        } else {
                            starIcon.classList.remove('scale-110');
                        }
                    } else {
                        starIcon.classList.add('text-gray-300');
                        starIcon.classList.remove('text-yellow-500', 'scale-110');
                    }
                });
            }

            function resetStars() {
                starLabels.forEach(label => {
                    const starIcon = label.querySelector('.star-icon');
                    starIcon.classList.add('text-gray-300');
                    starIcon.classList.remove('text-yellow-500', 'scale-110');
                });
            }

            function updateRatingText(rating) {
                const texts = isArabic ? ratingTexts.ar : ratingTexts.en;
                ratingText.textContent = texts[rating - 1];
                ratingText.classList.remove('text-medical-600');
                ratingText.classList.add('text-yellow-600', 'font-semibold');
            }
        }
    </script>
@endsection
