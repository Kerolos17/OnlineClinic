<footer class="bg-gradient-to-br from-medical-900 to-medical-800 text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold">WellClinic</span>
                </div>
                <p class="text-medical-200 mb-4 max-w-md">
                    {{ app()->getLocale() == 'ar' ? 'منصة طبية موثوقة تربطك بأفضل الأطباء للاستشارات الطبية عبر الإنترنت.' : 'Your trusted medical platform connecting you with top doctors for online consultations.' }}
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="font-bold text-lg mb-4">{{ app()->getLocale() == 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-medical-200 hover:text-white transition-colors">{{ __('messages.home') }}</a></li>
                    <li><a href="{{ route('doctors.index') }}" class="text-medical-200 hover:text-white transition-colors">{{ __('messages.doctors') }}</a></li>
                    <li><a href="{{ route('specializations.index') }}" class="text-medical-200 hover:text-white transition-colors">{{ __('messages.specializations') }}</a></li>
                    <li><a href="{{ route('about') }}" class="text-medical-200 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'من نحن' : 'About' }}</a></li>
                    <li><a href="{{ route('contact') }}" class="text-medical-200 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact' }}</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="font-bold text-lg mb-4">{{ app()->getLocale() == 'ar' ? 'تواصل معنا' : 'Contact Us' }}</h3>
                <ul class="space-y-2 text-medical-200">
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>info@wellclinic.com</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>+1 (555) 123-4567</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 text-center text-medical-300">
            <p>&copy; {{ date('Y') }} WellClinic. {{ app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
        </div>
    </div>
</footer>
