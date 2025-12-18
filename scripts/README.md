# Zoom Meeting Scripts

هذا المجلد يحتوي على scripts مساعدة لإدارة Zoom meetings.

## الملفات

### verify-zoom-fix.php

**الوصف:** التحقق الشامل من حالة نظام Zoom
**الاستخدام:**

```bash
php scripts/verify-zoom-fix.php
```

**يعرض:**

-   ✅ حالة إعدادات Zoom
-   ✅ عدد الـ Jobs المعلقة والفاشلة
-   ✅ عدد الحجوزات مع/بدون Zoom meetings
-   ✅ تفاصيل آخر 5 حجوزات
-   ✅ اختبار الاتصال بـ Zoom API

---

### test-zoom-api.php

**الوصف:** اختبار الاتصال بـ Zoom API
**الاستخدام:**

```bash
php scripts/test-zoom-api.php
```

**يعرض:**

-   نتيجة إنشاء meeting تجريبي
-   Meeting ID و Join URL

---

### process-zoom-jobs.php

**الوصف:** معالجة الحجوزات التي لا تحتوي على Zoom meetings
**الاستخدام:**

```bash
php scripts/process-zoom-jobs.php
```

**يقوم بـ:**

-   فحص الـ Jobs المعلقة
-   البحث عن حجوزات بدون Zoom meetings
-   إنشاء jobs جديدة لهذه الحجوزات
-   معالجة الـ jobs

---

### check-zoom-status.php

**الوصف:** عرض حالة جميع الحجوزات
**الاستخدام:**

```bash
php scripts/check-zoom-status.php
```

**يعرض:**

-   إحصائيات الحجوزات
-   تفاصيل كل حجز مع حالة Zoom meeting

---

## الاستخدام اليومي

### 1. التحقق السريع

```bash
php scripts/verify-zoom-fix.php
```

### 2. إصلاح مشاكل Zoom

```bash
# إذا وجدت حجوزات بدون Zoom meetings
php scripts/process-zoom-jobs.php
```

### 3. اختبار API

```bash
# إذا كنت تشك في مشكلة بالـ API
php scripts/test-zoom-api.php
```

## ملاحظات

-   جميع الـ scripts تستخدم Laravel bootstrap
-   تحتاج إلى تشغيلها من جذر المشروع
-   تتطلب إعدادات Zoom صحيحة في `.env`
