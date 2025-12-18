@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'من نحن' : 'About Us')

@section('content')
<!-- Hero Section -->
<div class="hero-gradient relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ app()->getLocale() == 'ar' ? 'من نحن' : 'About Us' }}
            </h1>
            <p class="text-xl text-medical-100 max-w-2xl mx-auto">
                {{ app()->getLocale() == 'ar'
                    ? 'نحن نؤمن بأن الرعاية الصحية يجب أن تكون متاحة للجميع، في أي وقت ومن أي مكان'
                    : 'We believe healthcare should be accessible to everyone, anytime, anywhere' }}
            </p>
        </div>
    </div>
</div>

<!-- Our Story Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">
                {{ app()->getLocale() == 'ar' ? 'قصتنا' : 'Our Story' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mt-2 mb-6">
                {{ app()->getLocale() == 'ar'
                    ? 'رحلتنا نحو رعاية صحية أفضل'
                    : 'Our Journey Towards Better Healthcare' }}
            </h2>
            <div class="space-y-4 text-medical-700 text-lg leading-relaxed">
                <p>
                    {{ app()->getLocale() == 'ar'
                        ? 'بدأت WellClinic من رؤية بسيطة: جعل الرعاية الصحية عالية الجودة متاحة للجميع. نحن نربط المرضى بأفضل الأطباء من خلال منصة آمنة وسهلة الاستخدام.'
                        : 'WellClinic started with a simple vision: making quality healthcare accessible to everyone. We connect patients with top doctors through a secure and easy-to-use platform.' }}
                </p>
                <p>
                    {{ app()->getLocale() == 'ar'
                        ? 'نحن نفهم أن وقتك ثمين، ولهذا قمنا ببناء منصة تتيح لك حجز استشارة طبية في دقائق معدودة، دون الحاجة للانتظار أو السفر.'
                        : 'We understand your time is precious, which is why we built a platform that lets you book a medical consultation in minutes, without waiting or traveling.' }}
                </p>
            </div>
        </div>
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-medical-100 to-accent-100 rounded-3xl transform rotate-3"></div>
            <div class="relative bg-white rounded-3xl p-8 shadow-xl">
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center p-6 bg-gradient-to-br from-medical-50 to-medical-100 rounded-2xl">
                        <div class="text-4xl mb-2">👨‍⚕️</div>
                        <div class="text-3xl font-bold text-medical-700 mb-1">{{ $stats['doctors_count'] }}{{ $stats['doctors_count'] > 0 ? '+' : '' }}</div>
                        <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'طبيب متخصص' : 'Specialist Doctors' }}</div>
                    </div>
                    <div class="text-center p-6 bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl">
                        <div class="text-4xl mb-2">😊</div>
                        <div class="text-3xl font-bold text-accent-700 mb-1">{{ $stats['satisfaction_rate'] }}%</div>
                        <div class="text-sm text-accent-600">{{ app()->getLocale() == 'ar' ? 'رضا المرضى' : 'Patient Satisfaction' }}</div>
                    </div>
                    <div class="text-center p-6 bg-gradient-to-br from-medical-50 to-medical-100 rounded-2xl">
                        <div class="text-4xl mb-2">📅</div>
                        <div class="text-3xl font-bold text-medical-700 mb-1">{{ number_format($stats['completed_bookings']) }}{{ $stats['completed_bookings'] > 0 ? '+' : '' }}</div>
                        <div class="text-sm text-medical-600">{{ app()->getLocale() == 'ar' ? 'استشارة ناجحة' : 'Successful Consultations' }}</div>
                    </div>
                    <div class="text-center p-6 bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl">
                        <div class="text-4xl mb-2">🌍</div>
                        <div class="text-3xl font-bold text-accent-700 mb-1">{{ $stats['specializations_count'] }}{{ $stats['specializations_count'] > 0 ? '+' : '' }}</div>
                        <div class="text-sm text-accent-600">{{ app()->getLocale() == 'ar' ? 'تخصص طبي' : 'Medical Specializations' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Our Values Section -->
<div class="bg-gradient-to-br from-medical-50 to-accent-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">
                {{ app()->getLocale() == 'ar' ? 'قيمنا' : 'Our Values' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mt-2 mb-4">
                {{ app()->getLocale() == 'ar' ? 'ما نؤمن به' : 'What We Believe In' }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-4xl mb-6">
                    🎯
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-3">
                    {{ app()->getLocale() == 'ar' ? 'الجودة أولاً' : 'Quality First' }}
                </h3>
                <p class="text-medical-600 leading-relaxed">
                    {{ app()->getLocale() == 'ar'
                        ? 'نختار أطباءنا بعناية فائقة لضمان حصولك على أفضل رعاية طبية ممكنة.'
                        : 'We carefully select our doctors to ensure you receive the best possible medical care.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center text-4xl mb-6">
                    🔒
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-3">
                    {{ app()->getLocale() == 'ar' ? 'الخصوصية والأمان' : 'Privacy & Security' }}
                </h3>
                <p class="text-medical-600 leading-relaxed">
                    {{ app()->getLocale() == 'ar'
                        ? 'بياناتك الطبية محمية بأعلى معايير الأمان والخصوصية.'
                        : 'Your medical data is protected with the highest security and privacy standards.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-medical-500 to-medical-600 rounded-2xl flex items-center justify-center text-4xl mb-6">
                    ⚡
                </div>
                <h3 class="text-xl font-bold text-medical-900 mb-3">
                    {{ app()->getLocale() == 'ar' ? 'السرعة والكفاءة' : 'Speed & Efficiency' }}
                </h3>
                <p class="text-medical-600 leading-relaxed">
                    {{ app()->getLocale() == 'ar'
                        ? 'نوفر لك وقتك من خلال عملية حجز سريعة وسهلة.'
                        : 'We save your time through a fast and easy booking process.' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Our Mission Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="bg-gradient-to-br from-medical-600 to-medical-700 rounded-3xl p-12 md:p-16 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative">
            <span class="text-medical-200 font-semibold text-sm uppercase tracking-wider">
                {{ app()->getLocale() == 'ar' ? 'مهمتنا' : 'Our Mission' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-6">
                {{ app()->getLocale() == 'ar'
                    ? 'نسعى لتحسين حياة الناس'
                    : 'We Strive to Improve People\'s Lives' }}
            </h2>
            <p class="text-xl text-medical-100 max-w-3xl mx-auto leading-relaxed">
                {{ app()->getLocale() == 'ar'
                    ? 'مهمتنا هي جعل الرعاية الصحية عالية الجودة متاحة ومريحة وبأسعار معقولة للجميع. نحن نستخدم التكنولوجيا لربط المرضى بالأطباء المتخصصين، مما يوفر تجربة رعاية صحية سلسة وفعالة.'
                    : 'Our mission is to make quality healthcare accessible, convenient, and affordable for everyone. We use technology to connect patients with specialist doctors, providing a seamless and effective healthcare experience.' }}
            </p>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-br from-accent-50 to-medical-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mb-4">
            {{ app()->getLocale() == 'ar' ? 'انضم إلى آلاف المرضى الراضين' : 'Join Thousands of Satisfied Patients' }}
        </h2>
        <p class="text-xl text-medical-600 mb-8">
            {{ app()->getLocale() == 'ar'
                ? 'ابدأ رحلتك الصحية معنا اليوم'
                : 'Start your health journey with us today' }}
        </p>
        <a href="{{ route('doctors.index') }}" class="bg-gradient-to-r from-medical-600 to-medical-700 text-white px-10 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 inline-flex items-center gap-2">
            <span>{{ app()->getLocale() == 'ar' ? 'احجز الآن' : 'Book Now' }}</span>
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
