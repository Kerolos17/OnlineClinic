<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WellClinic - Your Trusted Healthcare Partner')</title>
    @vite(['resources/css/app.css', 'resources/css/booking-enhancements.css', 'resources/js/app.js', 'resources/js/booking.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body
    class="bg-gradient-to-br from-medical-50 via-white to-medical-50 min-h-screen {{ app()->getLocale() == 'ar' ? 'font-cairo' : 'font-inter' }}">
    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Navigation -->
    <x-navigation />

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-20rem)]">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />
</body>

</html>
