<?php

/**
 * ملف اختبار نظام الإشعارات
 * Test Notifications System
 *
 * هذا الملف لاختبار نظام الإشعارات يدوياً
 * This file is for manually testing the notifications system
 *
 * الاستخدام | Usage:
 * php artisan tinker
 * include 'test-notifications.php';
 */

use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingNotification;

echo "=== اختبار نظام الإشعارات | Testing Notifications System ===\n\n";

// 1. التحقق من وجود أدمن
echo "1. البحث عن مستخدمين Admin...\n";
$admins = User::where('role', 'admin')->get();
echo "   عدد الأدمن: {$admins->count()}\n\n";

if ($admins->isEmpty()) {
    echo "   ⚠️  تحذير: لا يوجد مستخدمين بصلاحية Admin!\n";
    echo "   يرجى إنشاء مستخدم Admin أولاً.\n\n";
    exit;
}

// 2. التحقق من وجود حجوزات
echo "2. البحث عن حجوزات...\n";
$bookings = Booking::latest()->take(5)->get();
echo "   عدد الحجوزات: {$bookings->count()}\n\n";

if ($bookings->isEmpty()) {
    echo "   ⚠️  تحذير: لا توجد حجوزات في النظام!\n";
    echo "   يرجى إنشاء حجز أولاً.\n\n";
    exit;
}

// 3. اختبار إرسال إشعار
echo "3. اختبار إرسال إشعار...\n";
$testBooking = $bookings->first();
$testAdmin = $admins->first();

try {
    $testAdmin->notify(new NewBookingNotification($testBooking));
    echo "   ✅ تم إرسال الإشعار بنجاح!\n\n";
} catch (Exception $e) {
    echo "   ❌ خطأ في إرسال الإشعار: {$e->getMessage()}\n\n";
    exit;
}

// 4. التحقق من الإشعارات
echo "4. التحقق من الإشعارات...\n";
$notifications = $testAdmin->notifications()->latest()->take(5)->get();
echo "   عدد الإشعارات: {$notifications->count()}\n";
echo "   الإشعارات غير المقروءة: {$testAdmin->unreadNotifications->count()}\n\n";

// 5. عرض آخر إشعار
if ($notifications->isNotEmpty()) {
    echo "5. آخر إشعار:\n";
    $lastNotification = $notifications->first();
    echo "   العنوان: {$lastNotification->data['title']}\n";
    echo "   الرسالة: {$lastNotification->data['message']}\n";
    echo "   الوقت: {$lastNotification->created_at->diffForHumans()}\n";
    echo "   مقروء: " . ($lastNotification->read_at ? 'نعم' : 'لا') . "\n\n";
}

// 6. حجوزات اليوم
echo "6. حجوزات اليوم:\n";
$todayBookings = Booking::whereDate('appointment_at', today())->get();
echo "   عدد حجوزات اليوم: {$todayBookings->count()}\n\n";

if ($todayBookings->isNotEmpty()) {
    echo "   تفاصيل الحجوزات:\n";
    foreach ($todayBookings as $booking) {
        echo "   - #{$booking->id}: {$booking->patient_name} مع {$booking->doctor->user->name_en}\n";
        echo "     الوقت: {$booking->appointment_at->format('h:i A')}\n";
        echo "     الحالة: {$booking->status}\n\n";
    }
}

echo "=== انتهى الاختبار | Test Completed ===\n";
