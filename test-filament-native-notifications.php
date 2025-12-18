<?php

/**
 * اختبار Filament Native Database Notifications
 * Test Filament Native Database Notifications
 *
 * الاستخدام | Usage:
 * php artisan tinker
 * include 'test-filament-native-notifications.php';
 */

use App\Models\Booking;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

echo "=== اختبار Filament Native Notifications ===\n\n";

// 1. التحقق من وجود أدمن
echo "1. البحث عن مستخدمين Admin...\n";
$admins = User::where('role', 'admin')->get();
echo "   عدد الأدمن: {$admins->count()}\n\n";

if ($admins->isEmpty()) {
    echo "   ⚠️  تحذير: لا يوجد مستخدمين بصلاحية Admin!\n";
    echo "   يرجى إنشاء مستخدم Admin أولاً.\n\n";
    exit;
}

$admin = $admins->first();

// 2. اختبار إشعار Success
echo "2. اختبار إشعار Success...\n";
try {
    Notification::make()
        ->title('✅ نجاح | Success')
        ->body('هذا إشعار نجاح تجريبي')
        ->success()
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار Success بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 3. اختبار إشعار Warning
echo "3. اختبار إشعار Warning...\n";
try {
    Notification::make()
        ->title('⚠️ تحذير | Warning')
        ->body('هذا إشعار تحذير تجريبي')
        ->warning()
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار Warning بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 4. اختبار إشعار Danger
echo "4. اختبار إشعار Danger...\n";
try {
    Notification::make()
        ->title('❌ خطأ | Error')
        ->body('هذا إشعار خطأ تجريبي')
        ->danger()
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار Danger بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 5. اختبار إشعار Info
echo "5. اختبار إشعار Info...\n";
try {
    Notification::make()
        ->title('ℹ️ معلومة | Info')
        ->body('هذا إشعار معلومات تجريبي')
        ->info()
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار Info بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 6. اختبار إشعار مع أيقونة مخصصة
echo "6. اختبار إشعار مع أيقونة مخصصة...\n";
try {
    Notification::make()
        ->title('🎨 إشعار مخصص | Custom')
        ->body('إشعار مع أيقونة وألوان مخصصة')
        ->icon('heroicon-o-star')
        ->iconColor('warning')
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار مخصص بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 7. اختبار إشعار مع زر واحد
echo "7. اختبار إشعار مع زر...\n";
try {
    Notification::make()
        ->title('📋 إشعار مع زر | With Action')
        ->body('اضغط على الزر للانتقال')
        ->icon('heroicon-o-arrow-right')
        ->iconColor('info')
        ->actions([
            Action::make('view')
                ->label('عرض | View')
                ->url('/admin')
                ->button()
                ->markAsRead(),
        ])
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار مع زر بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 8. اختبار إشعار مع أزرار متعددة
echo "8. اختبار إشعار مع أزرار متعددة...\n";
try {
    Notification::make()
        ->title('🎯 إشعار مع أزرار | Multiple Actions')
        ->body('اختر أحد الخيارات')
        ->icon('heroicon-o-question-mark-circle')
        ->iconColor('primary')
        ->actions([
            Action::make('approve')
                ->label('موافقة | Approve')
                ->color('success')
                ->button(),
            Action::make('reject')
                ->label('رفض | Reject')
                ->color('danger')
                ->button(),
        ])
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار مع أزرار متعددة بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 9. اختبار إشعار حجز (محاكاة)
echo "9. اختبار إشعار حجز...\n";
$booking = Booking::latest()->first();

if ($booking) {
    try {
        Notification::make()
            ->title('🎉 حجز جديد | New Booking')
            ->body("حجز جديد من **{$booking->patient_name}** مع الدكتور **{$booking->doctor->user->name_en}**")
            ->icon('heroicon-o-calendar')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('عرض | View')
                    ->url(route('filament.admin.resources.bookings.edit', $booking->id))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admin, isEventDispatched: true);

        echo "   ✅ تم إرسال إشعار الحجز بنجاح!\n\n";
    } catch (Exception $e) {
        echo "   ❌ خطأ: {$e->getMessage()}\n\n";
    }
} else {
    echo "   ⚠️  لا توجد حجوزات في النظام\n\n";
}

// 10. اختبار إشعار مع Markdown
echo "10. اختبار إشعار مع Markdown...\n";
try {
    Notification::make()
        ->title('📝 إشعار مع Markdown')
        ->body("هذا نص **غامق** و *مائل* و [رابط](https://example.com)")
        ->icon('heroicon-o-document-text')
        ->iconColor('info')
        ->sendToDatabase($admin, isEventDispatched: true);

    echo "   ✅ تم إرسال إشعار مع Markdown بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: {$e->getMessage()}\n\n";
}

// 11. عرض الإحصائيات
echo "11. الإحصائيات:\n";
echo "   إجمالي الإشعارات: {$admin->notifications()->count()}\n";
echo "   الإشعارات غير المقروءة: {$admin->unreadNotifications->count()}\n";
echo "   الإشعارات المقروءة: " . ($admin->notifications()->count() - $admin->unreadNotifications->count()) . "\n\n";

// 12. عرض آخر 5 إشعارات
echo "12. آخر 5 إشعارات:\n";
$latestNotifications = $admin->notifications()->take(5)->get();

foreach ($latestNotifications as $index => $notification) {
    $num = $index + 1;
    $title = $notification->data['title'] ?? 'بدون عنوان';
    $time = $notification->created_at->diffForHumans();
    $status = $notification->read_at ? 'مقروء' : 'غير مقروء';

    echo "   {$num}. {$title}\n";
    echo "      الوقت: {$time}\n";
    echo "      الحالة: {$status}\n\n";
}

echo "=== انتهى الاختبار | Test Completed ===\n\n";

echo "📝 الخطوات التالية:\n";
echo "1. افتح لوحة التحكم: http://localhost/admin\n";
echo "2. ابحث عن أيقونة الجرس 🔔 في الـ Navbar (أعلى يمين)\n";
echo "3. ستجد Badge يعرض عدد الإشعارات غير المقروءة\n";
echo "4. اضغط على الجرس لفتح Modal الإشعارات\n";
echo "5. الإشعارات تتحدث تلقائياً كل 10 ثواني\n";
echo "6. جرب إنشاء حجز جديد لرؤية الإشعار Real-time\n\n";

echo "✨ استمتع بـ Filament Native Notifications! 🚀\n";
