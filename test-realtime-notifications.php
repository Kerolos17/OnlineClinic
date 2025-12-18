<?php

/**
 * اختبار الإشعارات Real-time
 * Test Real-time Notifications
 *
 * الاستخدام | Usage:
 * php artisan tinker
 * include 'test-realtime-notifications.php';
 */

use App\Models\Booking;
use App\Models\User;
use Filament\Notifications\Notification;

echo "=== اختبار الإشعارات Real-time | Testing Real-time Notifications ===\n\n";

// 1. التحقق من وجود أدمن
echo "1. البحث عن مستخدمين Admin...\n";
$admins = User::where('role', 'admin')->get();
echo "   عدد الأدمن: {$admins->count()}\n\n";

if ($admins->isEmpty()) {
    echo "   ⚠️  تحذير: لا يوجد مستخدمين بصلاحية Admin!\n";
    exit;
}

$admin = $admins->first();

// 2. اختبار إشعار Filament بسيط
echo "2. اختبار إشعار Filament بسيط...\n";
try {
    Notification::make()
        ->title('🎉 اختبار | Test')
        ->body('هذا إشعار تجريبي | This is a test notification')
        ->icon('heroicon-o-check-circle')
        ->iconColor('success')
        ->sendToDatabase($admin);

    echo "   ✅ تم إرسال الإشعار بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
    exit;
}

// 3. اختبار إشعار مع زر
echo "3. اختبار إشعار مع زر تفاعلي...\n";
try {
    Notification::make()
        ->title('📋 إشعار مع زر | Notification with Action')
        ->body('اضغط على الزر للانتقال | Click the button to navigate')
        ->icon('heroicon-o-arrow-right')
        ->iconColor('info')
        ->actions([
            \Filament\Notifications\Actions\Action::make('view')
                ->label('عرض | View')
                ->url('/admin')
                ->button(),
        ])
        ->sendToDatabase($admin);

    echo "   ✅ تم إرسال الإشعار مع الزر بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 4. اختبار إشعارات متعددة بألوان مختلفة
echo "4. اختبار إشعارات بألوان مختلفة...\n";

$notifications = [
    [
        'title' => '✅ نجاح | Success',
        'body' => 'إشعار نجاح | Success notification',
        'color' => 'success',
        'icon' => 'heroicon-o-check-circle',
    ],
    [
        'title' => '⚠️ تحذير | Warning',
        'body' => 'إشعار تحذير | Warning notification',
        'color' => 'warning',
        'icon' => 'heroicon-o-exclamation-triangle',
    ],
    [
        'title' => '❌ خطأ | Error',
        'body' => 'إشعار خطأ | Error notification',
        'color' => 'danger',
        'icon' => 'heroicon-o-x-circle',
    ],
    [
        'title' => 'ℹ️ معلومة | Info',
        'body' => 'إشعار معلومات | Info notification',
        'color' => 'info',
        'icon' => 'heroicon-o-information-circle',
    ],
];

foreach ($notifications as $notif) {
    try {
        Notification::make()
            ->title($notif['title'])
            ->body($notif['body'])
            ->icon($notif['icon'])
            ->iconColor($notif['color'])
            ->sendToDatabase($admin);

        echo "   ✅ {$notif['title']}\n";
    } catch (Exception $e) {
        echo "   ❌ خطأ في {$notif['title']}: {$e->getMessage()}\n";
    }
}

echo "\n";

// 5. اختبار إشعار حجز (محاكاة)
echo "5. اختبار إشعار حجز...\n";
$booking = Booking::latest()->first();

if ($booking) {
    try {
        Notification::make()
            ->title('🎉 حجز جديد | New Booking')
            ->body("حجز جديد من **{$booking->patient_name}** مع الدكتور **{$booking->doctor->user->name_en}**")
            ->icon('heroicon-o-calendar')
            ->iconColor('success')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('عرض | View')
                    ->url(route('filament.admin.resources.bookings.edit', $booking->id))
                    ->button(),
            ])
            ->sendToDatabase($admin);

        echo "   ✅ تم إرسال إشعار الحجز بنجاح!\n\n";
    } catch (Exception $e) {
        echo "   ❌ خطأ: {$e->getMessage()}\n\n";
    }
} else {
    echo "   ⚠️  لا توجد حجوزات في النظام\n\n";
}

// 6. عرض الإحصائيات
echo "6. الإحصائيات:\n";
echo "   إجمالي الإشعارات: {$admin->notifications()->count()}\n";
echo "   الإشعارات غير المقروءة: {$admin->unreadNotifications->count()}\n";
echo "   الإشعارات المقروءة: " . ($admin->notifications()->count() - $admin->unreadNotifications->count()) . "\n\n";

// 7. عرض آخر 3 إشعارات
echo "7. آخر 3 إشعارات:\n";
$latestNotifications = $admin->notifications()->take(3)->get();

foreach ($latestNotifications as $index => $notification) {
    $num = $index + 1;
    echo "   {$num}. {$notification->data['title']}\n";
    echo "      الوقت: {$notification->created_at->diffForHumans()}\n";
    echo "      مقروء: " . ($notification->read_at ? 'نعم' : 'لا') . "\n\n";
}

echo "=== انتهى الاختبار | Test Completed ===\n\n";

echo "📝 الخطوات التالية:\n";
echo "1. افتح لوحة التحكم: http://localhost/admin\n";
echo "2. ستجد الإشعارات في Widget الإشعارات\n";
echo "3. الإشعارات تتحدث تلقائياً كل 10 ثواني\n";
echo "4. جرب إنشاء حجز جديد لرؤية الإشعار Real-time\n\n";

echo "✨ استمتع بالإشعارات Real-time! 🚀\n";
