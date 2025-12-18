<nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-medical-100">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-18 lg:h-20">
            <!-- Logo -->
            <div class="flex items-center {{ app()->getLocale() == 'ar' ? 'order-2' : 'order-1' }}">
                <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 lg:w-12 lg:h-12 bg-gradient-to-br from-medical-500 to-medical-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="hidden xs:block">
                        <span class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-medical-600 to-medical-500 bg-clip-text text-transparent">WellClinic</span>
                        <p class="text-[10px] sm:text-xs text-medical-600 font-medium -mt-0.5 sm:-mt-1 hidden sm:block">{{ app()->getLocale() == 'ar' ? 'شريكك الصحي الموثوق' : 'Your Trusted Healthcare Partner' }}</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-reverse space-x-6 xl:space-x-8 order-1' : 'space-x-6 xl:space-x-8 order-2' }}">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>{{ __('messages.home') }}</span>
                </a>
                <a href="{{ route('doctors.index') }}" class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>{{ __('messages.doctors') }}</span>
                </a>
                <a href="{{ route('specializations.index') }}" class="nav-link {{ request()->routeIs('specializations.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>{{ __('messages.specializations') }}</span>
                </a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ app()->getLocale() == 'ar' ? 'من نحن' : 'About' }}</span>
                </a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact' }}</span>
                </a>

                <!-- Language Switcher -->
                <div class="flex items-center gap-2 {{ app()->getLocale() == 'ar' ? 'mr-3 xl:mr-4' : 'ml-3 xl:ml-4' }}">
    <a href="{{ route('lang.switch', 'en') }}"
       class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">
        EN
    </a>
    <a href="{{ route('lang.switch', 'ar') }}"
       class="lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
        عربي
    </a>
</div>

            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-medical-50 transition-colors {{ app()->getLocale() == 'ar' ? 'order-1' : 'order-3' }}">
                <svg class="w-6 h-6 text-medical-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden border-t border-medical-100 bg-white shadow-lg">
        <div class="px-3 sm:px-4 py-3 sm:py-4 space-y-1 sm:space-y-2 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ __('messages.home') }}</span>
            </a>
            <a href="{{ route('doctors.index') }}" class="mobile-nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ __('messages.doctors') }}</span>
            </a>
            <a href="{{ route('specializations.index') }}" class="mobile-nav-link {{ request()->routeIs('specializations.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>{{ __('messages.specializations') }}</span>
            </a>
            <a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ app()->getLocale() == 'ar' ? 'من نحن' : 'About' }}</span>
            </a>
            <a href="{{ route('contact') }}" class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>{{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact' }}</span>
            </a>
            
            <!-- Language Switcher Mobile -->
            <div class="flex gap-2 pt-3 sm:pt-4 border-t border-medical-100">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn flex-1 justify-center {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                    English
                </a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn flex-1 justify-center {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                    عربي
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

