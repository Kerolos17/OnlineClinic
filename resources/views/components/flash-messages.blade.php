@if (session('success'))
    <div class="fixed top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-50 animate-slide-in"
        id="flash-success">
        <div
            class="bg-gradient-to-r from-accent-500 to-accent-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 max-w-md">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
            <button onclick="this.parentElement.parentElement.remove()"
                class="ml-auto {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} hover:bg-white/20 rounded-lg p-1 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-success');
            if (flash) {
                flash.classList.add('animate-slide-out');
                setTimeout(() => flash.remove(), 300);
            }
        }, 5000);
    </script>
@endif

@if (session('error'))
    <div class="fixed top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-50 animate-slide-in"
        id="flash-error">
        <div
            class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 max-w-md">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
            <button onclick="this.parentElement.parentElement.remove()"
                class="ml-auto {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} hover:bg-white/20 rounded-lg p-1 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-error');
            if (flash) {
                flash.classList.add('animate-slide-out');
                setTimeout(() => flash.remove(), 5000);
            }
        }, 5000);
    </script>
@endif

@if (session('warning'))
    <div class="fixed top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-50 animate-slide-in"
        id="flash-warning">
        <div
            class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 max-w-md">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('warning') }}</span>
            <button onclick="this.parentElement.parentElement.remove()"
                class="ml-auto {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} hover:bg-white/20 rounded-lg p-1 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-warning');
            if (flash) {
                flash.classList.add('animate-slide-out');
                setTimeout(() => flash.remove(), 5000);
            }
        }, 5000);
    </script>
@endif

@if ($errors->any())
    <div class="fixed top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-50 animate-slide-in"
        id="flash-errors">
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl max-w-md">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-2">
                        {{ app()->getLocale() == 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}
                    </p>
                    <ul class="text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="text-white/80">•</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove()"
                    class="hover:bg-white/20 rounded-lg p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-errors');
            if (flash) {
                flash.classList.add('animate-slide-out');
                setTimeout(() => flash.remove(), 7000);
            }
        }, 7000);
    </script>
@endif
