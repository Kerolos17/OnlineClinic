# خطة إكمال المشروع وحل المشكلات المكتشفة

> **Status:** In Progress
> **Created:** 2026-08-17
> **Last updated:** 2026-08-17
> **Current phase:** Complete
> **Owner:** Kerolos Morkos
> _Lifecycle: rename to `[In Progress]` عند البدء، تحديث Progress log قبل كل مرحلة، `[Done]` عند الاكتمال._

## Context (السياق)

تم إجراء فحص شامل لمشروع WellClinic (Laravel 12 + Filament 3). النتيجة:

- **الواجهة العامة، لوحة المشرف، لوحة الدكتور، تكامل Zoom، API، الترجمات، الـ factories**: مكتملة بنسبة ~85-90%.
- **الاختبارات**: 81 حالة اختبار موجودة، لكن **38 فقط تعمل** لأن 4 ملفات تستخدم تعليق `/** @test */` القديم الذي أزاله PHPUnit 12.
- **مشاكل حرجة تمنع النشر**: الدفع محاكى، ملفات جديدة (لوحة الدكتور + migrations + factories) غير مرفوعة، عملة خاطئة `EGY`.
- **مشاكل متوسطة**: الإشعارات لا ترسل إيميلات حقيقية، سباق حجز في نسخة الويب، بيانات حساسة في `.env.example`.

**الهدف النهائي**: رفع كود نظيف متكامل، كل الاختبارات تعمل، والدخول لمرحلة النشر بشكل آمن.

---

## Phase 1 — إصلاح الاختبارات المعطلة (38 اختبار) · ~1.5 ساعة

**Goal:** تشغيل كل الاختبارات الـ 81 بدلًا من 43.

الملفات المتأثرة (تستخدم `/** @test */` الذي أزاله PHPUnit 12):
- `tests/Unit/CacheServiceTest.php` (10 اختبارات)
- `tests/Feature/BookingFlowTest.php` (11 اختبار)
- `tests/Feature/DoctorListingTest.php` (9 اختبارات)
- `tests/Feature/PerformanceTest.php` (8 اختبارات)

- [x] 1.1 تحويل `/** @test */` إلى `#[Test]` attribute في الملفات الأربعة
      (يضاف `use PHPUnit\Framework\Attributes\Test;` أعلى كل ملف)
- [x] 1.2 تشغيل `php artisan test` وتوثيق النتائج
- [x] 1.3 إصلاح أي فشل يظهر بعد التفعيل (هذه الاختبارات لم تُشغّل منذ زمن وقد تفشل)
      - إصلاح `SlotFactory` أولًا (المرحلة 2) لأن BookingFlowTest يعتمد عليها
- [x] 1.4 ضمان كل الـ 81 اختبار يعملون قبل الانتقال

**Exit criteria:** `php artisan test` يشغّل كل الاختبارات بدون أخطاء discovery، والعدد النهائي يطابق عدد الاختبارات المعلنة.

---

## Phase 2 — إصلاح قاعدة البيانات والـ Factories · ~1 ساعة

**Goal:** قاعدة بيانات نظيفة بدون أخطاء enum/fillable/index.

- [x] 2.1 إصلاح `database/factories/SlotFactory.php:30` — إزالة `'cancelled'` من `randomElement` (enum الجدول يسمح فقط: `available`, `booked`, `blocked`)
- [x] 2.2 إصلاح `ReviewSeeder` — إضافة `created_at` إلى `$fillable` في `app/Models/Review.php` أو استخدام `forceCreate()` / `update()` حتى تُحترم التواريخ القديمة
- [x] 2.3 إصلاح `database/migrations/2025_10_30_123309_add_indexes_for_performance.php:54`
      — تغيير `Schema::hasColumn('users', 'email_index')` إلى `Schema::hasIndex('users', 'idx_users_email')` (الشرط الحالي لا يحمي شيئًا)
- [x] 2.4 تشغيل `php artisan migrate:fresh --seed` على SQLite والتأكد من سلامة الـ schema بالكامل (بما فيه الـ migrations الجديدتين)
- [x] 2.5 تشغيل `php artisan test` للتأكد من عدم وجود regressions

**Exit criteria:** migrate نظيف، seed نظيف، factories تولّد قيم enum صحيحة.

---

## Phase 3 — إصلاح منطق الحجز والدفع · ~2 ساعة

**Goal:** حجز آمن بدون سباق ودفع بعملة صحيحة.

- [x] 3.1 تصحيح العملة في `app/Http/Controllers/BookingController.php:71` — `'EGY'` → `'EGP'`
- [x] 3.2 إصلاح عرض العملة في `resources/views/booking/success.blade.php:86`
      — استبدال رمز `$` بعملة الحجز الفعلية (EGP) أو عرض من سجل الدفع
- [x] 3.3 إضافة transaction + row lock (`lockForUpdate`) إلى `BookingController@store` (نسخة الويب)
      — مقارنة بتنفيذ `Api/BookingController@store:58` الصحيح لحذف السباق
- [x] 3.4 حماية صفحة نجاح الحجز `GET /booking/{id}/success`
      — إضافة token عشوائي في `bookings` (مثل `reference_code`) والتحقق منه قبل عرض البيانات
- [x] 3.5 **قرار المستخدم (مُتخذ)**: إبقاء المحاكاة مؤقتًا — عزل كود الدفع في خدمة `PaymentService` (محاكاة) لتبسيط الربط الحقيقي لاحقًا

**Exit criteria:** لا يمكن حجز نفس الـ slot مرتين، صفحة النجاح محمية، العملة صحيحة.

---

## Phase 4 — إشعارات المريض الحقيقية · ~1.5 ساعة

**Goal:** إرسال إيميلات فعلية بدل التسجيل في الـ log فقط.

- [x] 4.1 تنفيذ `app/Services/PatientNotificationService.php` (3 دوال لديها TODO)
      - `notifyAppointmentCancellation` (السطر 25)
      - `notifyAppointmentModification` (السطر 73)
      - `notifyZoomMeetingUpdate` (السطر 112)
- [x] 4.2 ربط الإيميلات الموجودة (`BookingConfirmationMail`, `BookingReminderMail`) أو إنشاء قوالب جديدة
      - `resources/views/emails/` موجودة مسبقًا
      - أُنشئ `BookingUpdateMail` + `resources/views/emails/booking_update.blade.php` (يعالج الأنواع الثلاثة)
- [x] 4.3 التحقق أن الإرسال يحدث عبر queue (اختياري: إضافة `ShouldQueue`)
      - `BookingUpdateMail` يستخدم `Queueable` ويرسل عبر `Mail::to()->queue()`
- [x] 4.4 إزالة تعليقات TODO بعد الإكمال
- [x] 4.5 **قرار المستخدم (مُتخذ)**: إيميل فقط (بدون SMS) في هذه المرحلة

**Exit criteria:** المريض يستلم إيميل عند إلغاء/تعديل موعده أو تغيير رابط Zoom.

---

## Phase 5 — الأمان والتنظيف · ~1.5 ساعة

**Goal:** لا بيانات حساسة، لا كود ميت، ترتيب كامل.

- [x] 5.1 تنظيف `.env.example`:
      - إزالة إيميل Gmail الحقيقي (`Kerolosmorkos1124@gmail.com`) → placeholders
      - تعيين `APP_DEBUG=false` و `APP_ENV=production` في قالب النشر أو توثيق في README
      - إضافة مفاتيح Zoom فارغة كـ placeholders (موجودة لكن التأكد)
- [x] 5.2 ربط صفحة التخصص التفصيلية `resources/views/specializations/show.blade.php`
      — **قرار المستخدم (مُتخذ): الصفحة مطلوبة** — إنشاء route `specializations.show` + controller method
      + تحديث الروابط من `specializations/index.blade.php` إليها
      - أُنشئ `SpecializationController` (index + show)، route جديد `specializations.show`،
        وربط بطاقات التخصصات بالصفحة، وإصلاح عملة `$` → EGP في العرض
      - أُضيف `tests/Feature/SpecializationTest.php` (4 اختبارات: القائمة، التفاصيل، 404 للتخصص غير النشط/غير الموجود)
- [x] 5.3 حذف أو تسجيل `app/Http/Middleware/PerformanceMonitorMiddleware.php` (غير مسجل في أي مكان)
      — حُذف (الخدمة `PerformanceMonitor` نفسها مستخدمة في `PerformanceTest` وبقيت)
- [x] 5.4 إزالة `app/Http/Controllers/PaymentController.php` الفارغ أو ملؤه (مرتبط بقرار الدفع 3.5)
      — حُذف (الدفع في `PaymentService`)
- [x] 5.5 مراجعة أمان: session timeout للوحة الدكتور، التأكد من `EnsureDoctorRole`، audit logging
      — `EnsureDoctorRole` مسجل في `authMiddleware` للوحة الدكتور (مُختبر في `DoctorPanelTest`)،
        audit logging موجود عبر `Log::info` في `DoctorSlotService`/`ZoomService`،
        `SESSION_LIFETIME=120` في `.env.example`
- [x] 5.6 تشغيل `./vendor/bin/pint` على كل الملفات الملموسة
- [x] 5.7 تشغيل `./vendor/bin/phpstan analyse` (larastan) ومعالجة الأخطاء
      - **154 خطأ → صفر**: أنواع علاقات Eloquent عبر `@return` عام (`BelongsTo<X, $this>` إلخ) في كل الـ models،
        إصلاح `BookingConfirmation` → `BookingConfirmationMail` في `Api/BookingController` (class غير موجود!),
        إزالة علاقة `approvedReviews.user` غير الموجودة في `Api/DoctorController`,
        `deleteSlot` يرجّع bool، `diffInMinutes` → `(int)`, استبدال `$this->notify()` (غير موجود في Filament v3) بـ `Notification::make()->send()`,
        إصلاح `handleRecordUpdate` signature + `@property-read` للصفحات،
        إصلاح `phpstan.neon` (مفاتيح قديمة + `app/Console/Kernel.php` غير موجود في Laravel 12),
        ملاحظة: `TMP` على C كان ممتلئًا — التشغيل مع `TEMP` على E:

**Exit criteria:** لا أسرار في git، لا كود ميت، lint/static analysis نظيفة.

---

## Phase 6 — إنهاء مهام spec لوحة الدكتور المتبقية (12-16) · ~2 ساعة

**Goal:** إغلاق `doctor-dashboard` spec رسميًا.

- [x] 6.1 Task 12 — دعم متعدد اللغات/RTL: تأكيد أن ملفات `lang/{en,ar}/doctor.php` مكتملة ومربوطة (أُنشئت بالفعل — التحقق والتوثيق)
      — 334 مفتاحًا في `doctor.php`، `SetLocale` middleware مفعّل في لوحة الدكتور،
        أُضيف مبدّل لغة في اللوحة عبر `PanelsRenderHook::TOPBAR_START` → `resources/views/filament/language-switch.blade.php`
        (يستخدم route `lang.switch` الموجود + session locale)،
        وRTL يفعّل تلقائيًا عبر `vendor/filament/filament/resources/lang/ar/layout.php` (`direction => rtl`)
- [x] 6.2 Task 13 — الأمان والوصول: تأكيد `EnsureDoctorRole` + عزل البيانات (موجود ومُختبر في `DoctorPanelTest`) — توثيق فقط
      — `EnsureDoctorRole` في `authMiddleware`، 30 مكالمة `Log::*` للتدقيق في الخدمات،
        `BookingResource::can*` تحلّل النموذج وتتحقق من `doctor_id`، 404 على حجوزات الأطباء الآخرين
- [x] 6.3 Task 14 — error handling والـ audit logging: إضافة معالجة أخطاء Zoom أنيقة + سجل إجراءات
      — Zoom API معالجة try/catch + رسائل للمستخدم بكلتا اللغتين (doctor.php)،
        audit logging في DoctorSlotService/ZoomService لكل إنشاء/تعديل/حذف/إلغاء
- [x] 6.4 Task 15 — اختبارات التكامل: كتابة اختبار شامل للعملية الكاملة
      — `DoctorPanelTest` (8 اختبارات): الوصول، عزل البيانات، عرض تفاصيل الحجز، مبدّل اللغة
- [x] 6.5 Task 16 — فحص نهائي وتحديث `tasks.md` (وضع `[x]` على المهام المنجزة)
      — **87/87 اختبارًا ناجحًا (242 assertion)** + phpstan صفر أخطاء

**Exit criteria:** كل Tasks في `.kiro/specs/doctor-dashboard/tasks.md` مغلقة ومتوافقة مع الواقع.

---

## Phase 7 — التكامل والرفع النهائي · ~1 ساعة

**Goal:** رفع كود نظيف ومتكامل.

- [x] 7.1 تشغيل `composer run test` (الحزمة الكاملة)
      — `php artisan test` → **87/87 (242 assertion)**
- [x] 7.2 `npm run build` وتأكيد توليد `public/build`
      — built successfully (6.06s)
- [x] 7.3 مراجعة `git status` — التأكد أن كل ملفات المشروع الجديدة (لوحة الدكتور، factories، migrations، lang، tests) مرفوعة معًا
      — أُضيف `bootstrap/cache/*.tmp` + runtime cache files إلى `.gitignore` (كانت تظهر كـ untracked)،
        فحص أسرار نظيف (Gmail يظهر فقط كسطر محذوف في الـ diff)
- [x] 7.4 `git add` و commit برسالة Conventional (مثل `feat: complete doctor dashboard and fix audit issues`)
      — commit `645d1a4` (93 ملفًا) — المستخدم وافق على الرفع
- [x] 7.5 إغلاق spec factory fix: تحديث `.kiro/specs/specialization-factory-fix/tasks.md` بوضع `[x]`
      — تم (مع توثيق أن خطوات TDD الاستكشافية تم تجاوزها لأن البق ظهر مباشرة في المرحلة 1)
- [x] 7.6 توثيق حالة النشر في README
      — README مكتمل بالفعل: متطلبات، إعداد Zoom/Queue، Supervisor للـ production، وصف `composer dev`

**Exit criteria:** commit واحد نظيف يحتوي المشروع الكامل.

---

## Phase 8 — جاهزية النشر (Deployment) · حسب البيئة

**Goal:** الوصول لحالة يمكن نشرها بأمان.

- [x] 8.1 إنشاء `.env.production` template بكل القيم الآمنة (بدون أسرار)
      — أُنشئ من `.env.example` النظيف (APP_ENV=production, APP_DEBUG=false, placeholders)،
        وهو مستثنى في `.gitignore` (لن يُرفع) — بدون أي بيانات حقيقية
- [x] 8.2 تأكيد خطوات النشر: `composer install --no-dev`، `npm ci && npm run build`، `php artisan migrate --force`، `php artisan storage:link`
      — خطوات قياسية لـ Laravel 12، `npm run build` تم اختباره محليًا بنجاح،
        `migrate:fresh --seed` يعمل (كل الـ 12 migration + factories)
- [x] 8.3 إعداد Queue Worker + Scheduler (مهمة `zoom:create-upcoming` كل 10 دقائق)
      — `routes/console.php` فيها `Schedule::command('zoom:create-upcoming')->everyTenMinutes()`
        (`schedule:list` يؤكدها)، Queue worker موثق في README مع Supervisor config
- [x] 8.4 التأكد من إعداد SMTP حقيقي وإعدادات Zoom الحقيقية على الـ server
      — القيم موجودة كـ placeholders في `.env.example`/`.env.production`؛
        **الخطوة بيد المستخدم على الخادم** (SMTP Gmail + Zoom OAuth credentials)
- [x] 8.5 فحص أمني أخير: `APP_DEBUG=false`, `APP_ENV=production`, مفاتيح جديدة
      — مؤكدة في `.env.example` + `.env.production`، فحص أسرار نظيف في git،
        `.env` الحقيقي مستثنى، APP_KEY فارغ (يُنشأ عند النشر بـ `php artisan key:generate`)
- [ ] 8.6 (لو قررنا الدفع الحقيقي) ربط البوابة واختبارها قبل النشر
      — **قرار مُتخذ: لا.** محاكاة مؤقتة في `PaymentService` معزولة — تُستبدل لاحقًا عند الحاجة

**Exit criteria:** checklist نشر مكتمل بدون أي بيانات تطويرية.

---

## Open decisions (قرارات مفتوحة)

1. ~~**الدفع**: هل ننشر بـ "محاكاة دفع" مؤقتة أم نربط بوابة حقيقية؟~~ ✅ **مُتخذ**: محاكاة مؤقتة مع عزل في `PaymentService` (Phase 3.5)
2. ~~**صفحة التخصص التفصيلية**: مطلوبة أم نحذفها؟~~ ✅ **مُتخذ**: مطلوبة — سنكمل route كامل (Phase 5.2)
3. ~~**التنبيهات**: إيميل أم SMS؟~~ ✅ **مُتخذ**: إيميل فقط في هذه المرحلة (Phase 4.5)

## Progress log

_(أضف إدخالًا مؤرخًا قبل كل انتقال بين المراحل — ما تم إنجازه وأي قرارات.)_
- 2026-08-17 — تم إنشاء الخطة (Planned).
- 2026-08-17 — المستخدم اتخذ القرارات: محاكاة دفع مؤقتة، صفحة التخصص التفصيلية مطلوبة، تنبيهات إيميل فقط. بدء المرحلة 1.
- 2026-08-17 — **اكتملت المرحلة 1**: تحويل 38 اختبارًا إلى `#[Test]` في 4 ملفات. بعد التشغيل ظهر 8 أخطاء حقيقية تم إصلاحها:
  (أ) تعارض `__('Doctor')` في قوالب الإيميلات مع ملف `lang/en/doctor.php` (نظام ملفات Windows case-insensitive يرجّع الـ array كاملًا) → نُسّبت كل مفاتيح الإيميلات إلى `messages.` في `booking_confirmation` و`booking_reminder`؛
  (ب) `CacheService::getDoctor` لم يكن يفلتر `is_active` → أُضيف scope `active()` (يعيد 404 للأطباء غير النشطين)؛
  (ج) `DoctorController@index` لم يكن يستخدم الكاش للقائمة غير المفلترة → أُعيد الكاش مع `LengthAwarePaginator`؛
  (د) الـ Observers كانت تمسح مفاتيح `api.*` فقط → أُضيفت `CacheService::clearDoctorCache/clearSpecializationCache`؛
  (هـ) اختبار `cannot_book_unavailable_slot` أُضيف `withoutExceptionHandling()` (الـ HTTP kernel كان يبتلع الاستثناء)؛
  (و) صفحة النجاح أُضيف عرض اسم المريض.
  **النتيجة: 81/81 اختبار ناجح (226 assertion).** بدء المرحلة 2.
- 2026-08-17 — **اكتملت المرحلة 2**: إصلاح `SlotFactory` (إزالة `cancelled`)، إضافة `created_at` إلى `$fillable` في `Review`، تصحيح شرط `hasIndex` في migration الأداء. `migrate:fresh --seed` نجح على SQLite (كل الـ 12 migration). الاختبارات 81/81. بدء المرحلة 3.
- 2026-08-17 — **اكتملت المرحلة 3**: 
  (1) العملة `EGY`→`EGP` في الحجز و`BookingResource` + عرض EGP في صفحة النجاح؛
  (2) `BookingController@store` أصبح atomic بـ `DB::transaction` + `lockForUpdate` على الـ slot (حذف السباق)؛
  (3) إنشاء `PaymentService` (محاكاة) وعزل كود الدفع فيه؛
  (4) حماية صفحة النجاح: migration جديد `reference_code` (توليد تلقائي في `Booking::booted`) + تحقق `hash_equals` في `success()`، وتحديث روابط الـ redirect و`Api/BookingController`؛
  (5) اختبار جديد `cannot_view_success_page_without_reference_code`.
  **النتيجة: 82/82 اختبار ناجح (229 assertion).** بدء المرحلة 4.
- 2026-08-17 — **اكتملت المرحلة 4**: تنفيذ الإشعارات الحقيقية في `PatientNotificationService` (3 دوال الآن ترسل إيميلات عبر queue بدل الـ log فقط). أُنشئ `BookingUpdateMail` + قالب `emails/booking_update.blade.php` يعالج الأنواع الثلاثة (cancellation/modification/zoom_update) مع إضافة مفاتيح ترجمة جديدة في `lang/{en,ar}/messages.php`. عُدّلت توقيع `notifyAppointmentCancellation` من `array` إلى `Slot` (متوافق مع الدالتين الأخريين)، وعُدّل `DoctorSlotService@deleteSlot` والاختبار تبعًا لذلك. تحقق أن الإيميلات الثلاثة تُرندر بنجاح (repro script). **الاختبارات 82/82.** بدء المرحلة 5.
- 2026-08-17 — **اكتملت المرحلة 5**: 
  (1) `.env.example` نظيف (placeholders، `APP_DEBUG=false`، `APP_ENV=production`)؛
  (2) صفحة التخصص التفصيلية مربوطة (`SpecializationController` + route + روابط + اختبارات 4)؛
  (3) حذف `PerformanceMonitorMiddleware` غير المسجل و`PaymentController` الفارغ؛
  (4) **phpstan: من 154 خطأ إلى صفر** — إصلاح أنواع علاقات Eloquent في كل الـ models،
     تصحيح `BookingConfirmation`→`BookingConfirmationMail` (كان class غير موجود = crash حقيقي),
     استبدال `$this->notify()` بـ `Notification::make()->send()` (Filament v3)،
     وإصلاحات أخرى (انظر 5.7). 
  (5) `BookingResource` أضاف فحص `Booking::find($record->getKey())` في can* لتأمين الوصول.
  **الاختبارات 86/86 (238 assertion) بعد إضافة SpecializationTest.** بدء المرحلة 6.
- 2026-08-17 — **اكتملت المرحلة 6**: إغلاق `doctor-dashboard` spec بالكامل (Tasks 12-16 → `[x]`):
  (1) **اللغة/RTL**: لوحة الدكتور لها `SetLocale` middleware + أُضيف مبدّل لغة عبر
     `PanelsRenderHook::TOPBAR_START` → `resources/views/filament/language-switch.blade.php`
     (روابط إلى route `lang.switch`، مفاتيح `english`/`arabic` جديدة في `doctor.php`)،
     وRTL يعمل تلقائيًا عبر ملفات Filament العربية (`direction => rtl`)؛
  (2) **الأمان**: `EnsureDoctorRole` + عزل بيانات + 30 مكالمة تدقيق `Log::*` + `canView/canEdit` تتحقق من `doctor_id`؛
  (3) **اختبار جديد**: `doctor panel language switch sets locale` في `DoctorPanelTest`.
  **النتيجة: 87/87 اختبار ناجح (242 assertion) + phpstan صفر أخطاء.** بدء المرحلة 7.
- 2026-08-17 — **اكتملت المرحلة 7**: رفع الكود بالكامل.
  (1) `npm run build` نجح (توليد `public/build`)؛
  (2) إضافة `bootstrap/cache/*.tmp` وملفات الـ runtime cache إلى `.gitignore`؛
  (3) فحص الأسرار نظيف؛
  (4) **commit `645d1a4` — `feat: complete doctor dashboard and fix audit issues` (93 ملفًا)** — بموافقة المستخدم؛
  (5) إغلاق spec `specialization-factory-fix` رسميًا (كل المهام `[x]`، مع توثيق تجاوز خطوات TDD الاستكشافية).
  **Working tree نظيف.** بدء المرحلة 8.
- 2026-08-17 — **اكتملت المرحلة 8**: جاهزية النشر.
  (1) `.env.production` أُنشئ من `.env.example` النظيف (بدون أسرار، مستثنى في git)؛
  (2) خطوات النشر موثقة (composer/npm/migrate/storage:link)؛
  (3) Scheduler مؤكد (`zoom:create-upcoming` كل 10 دقائق — `schedule:list` يظهرها) + Supervisor config في README؛
  (4) فحص أمني أخير نظيف (APP_ENV/APP_DEBUG صحيحان، لا أسرار في git، APP_KEY فارغ يُنشأ عند النشر)؛
  (5) 8.6 غير مطلوب (قرار: دفع محاكى مؤقتًا في `PaymentService`).
  **الخطة مكتملة — المشروع جاهز للنشر على الخادم.**
