@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact Us')

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
                {{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact Us' }}
            </h1>
            <p class="text-xl text-medical-100 max-w-2xl mx-auto">
                {{ app()->getLocale() == 'ar'
                    ? 'نحن هنا لمساعدتك. تواصل معنا في أي وقت'
                    : 'We\'re here to help. Reach out to us anytime' }}
            </p>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div>
            <div class="mb-8">
                <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">
                    {{ app()->getLocale() == 'ar' ? 'تواصل معنا' : 'Get In Touch' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mt-2 mb-4">
                    {{ app()->getLocale() == 'ar' ? 'أرسل لنا رسالة' : 'Send Us a Message' }}
                </h2>
                <p class="text-medical-600 text-lg">
                    {{ app()->getLocale() == 'ar'
                        ? 'املأ النموذج وسنرد عليك في أقرب وقت ممكن'
                        : 'Fill out the form and we\'ll get back to you as soon as possible' }}
                </p>
            </div>

            @if(session('success'))
            <div class="bg-accent-50 border border-accent-200 text-accent-800 px-6 py-4 rounded-xl mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-medical-900 mb-2">
                        {{ app()->getLocale() == 'ar' ? 'الاسم' : 'Name' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-3 border border-medical-200 rounded-xl focus:ring-2 focus:ring-medical-500 focus:border-transparent transition-all"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسمك' : 'Enter your name' }}"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-medical-900 mb-2">
                        {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 border border-medical-200 rounded-xl focus:ring-2 focus:ring-medical-500 focus:border-transparent transition-all"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-medical-900 mb-2">
                        {{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                    </label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full px-4 py-3 border border-medical-200 rounded-xl focus:ring-2 focus:ring-medical-500 focus:border-transparent transition-all"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل رقم هاتفك' : 'Enter your phone number' }}"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-semibold text-medical-900 mb-2">
                        {{ app()->getLocale() == 'ar' ? 'الموضوع' : 'Subject' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" required
                           class="w-full px-4 py-3 border border-medical-200 rounded-xl focus:ring-2 focus:ring-medical-500 focus:border-transparent transition-all"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'موضوع الرسالة' : 'Message subject' }}"
                           value="{{ old('subject') }}">
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-medical-900 mb-2">
                        {{ app()->getLocale() == 'ar' ? 'الرسالة' : 'Message' }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" name="message" rows="6" required
                              class="w-full px-4 py-3 border border-medical-200 rounded-xl focus:ring-2 focus:ring-medical-500 focus:border-transparent transition-all resize-none"
                              placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب رسالتك هنا...' : 'Write your message here...' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-medical-600 to-medical-700 text-white px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                    <span>{{ app()->getLocale() == 'ar' ? 'إرسال الرسالة' : 'Send Message' }}</span>
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Contact Info -->
        <div>
            <div class="bg-gradient-to-br from-medical-50 to-accent-50 rounded-3xl p-8 md:p-10 h-full">
                <h3 class="text-2xl font-bold text-medical-900 mb-8">
                    {{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}
                </h3>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-medical-500 to-medical-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-medical-900 mb-1">
                                {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                            </h4>
                            <a href="mailto:info@wellclinic.com" class="text-medical-600 hover:text-medical-700 transition-colors">
                                info@wellclinic.com
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-medical-900 mb-1">
                                {{ app()->getLocale() == 'ar' ? 'الهاتف' : 'Phone' }}
                            </h4>
                            <a href="tel:+1234567890" class="text-medical-600 hover:text-medical-700 transition-colors">
                                +1 (234) 567-890
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-medical-500 to-medical-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-medical-900 mb-1">
                                {{ app()->getLocale() == 'ar' ? 'ساعات العمل' : 'Working Hours' }}
                            </h4>
                            <p class="text-medical-600">
                                {{ app()->getLocale() == 'ar' ? 'متاح 24/7' : 'Available 24/7' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-medical-900 mb-1">
                                {{ app()->getLocale() == 'ar' ? 'العنوان' : 'Address' }}
                            </h4>
                            <p class="text-medical-600">
                                {{ app()->getLocale() == 'ar' ? 'خدمة طبية أونلاين' : 'Online Medical Service' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-medical-200">
                    <h4 class="font-bold text-medical-900 mb-4">
                        {{ app()->getLocale() == 'ar' ? 'تابعنا' : 'Follow Us' }}
                    </h4>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-medical-600 hover:bg-medical-600 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-medical-600 hover:bg-medical-600 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-medical-600 hover:bg-medical-600 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-gradient-to-br from-medical-50 to-accent-50 py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-medical-600 font-semibold text-sm uppercase tracking-wider">
                {{ app()->getLocale() == 'ar' ? 'الأسئلة الشائعة' : 'FAQ' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-medical-900 mt-2 mb-4">
                {{ app()->getLocale() == 'ar' ? 'أسئلة متكررة' : 'Frequently Asked Questions' }}
            </h2>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="font-bold text-lg text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'كيف يمكنني حجز موعد؟' : 'How can I book an appointment?' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar'
                        ? 'يمكنك تصفح الأطباء المتاحين، اختيار الطبيب المناسب، ثم اختيار التاريخ والوقت المناسب لك.'
                        : 'You can browse available doctors, choose the right doctor, then select the date and time that suits you.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="font-bold text-lg text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'هل الاستشارات آمنة؟' : 'Are consultations secure?' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar'
                        ? 'نعم، جميع الاستشارات تتم عبر Zoom بشكل آمن ومشفر، وبياناتك محمية بالكامل.'
                        : 'Yes, all consultations are conducted via Zoom securely and encrypted, and your data is fully protected.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="font-bold text-lg text-medical-900 mb-2">
                    {{ app()->getLocale() == 'ar' ? 'ما هي طرق الدفع المتاحة؟' : 'What payment methods are available?' }}
                </h3>
                <p class="text-medical-600">
                    {{ app()->getLocale() == 'ar'
                        ? 'نقبل الدفع عبر Stripe، Paymob، أو الدفع النقدي.'
                        : 'We accept payment via Stripe, Paymob, or cash payment.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
