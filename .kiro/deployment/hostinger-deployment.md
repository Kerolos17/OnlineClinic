# دليل رفع WellClinic على Hostinger (Shared Hosting / hPanel)

> المشروع: Laravel 12 + Filament 3.3 — يتطلب PHP 8.2+ و MySQL 8+ و Node.js للبناء.

## الخطوة 1: تجهيزات على hPanel

1. **نوع PHP**: في hPanel → `Advanced → PHP Configuration` (أو من قسم Website) اختر **PHP 8.2 أو 8.3**.
2. **الدومين/الساب دومين**: 
   - من `Domains` أضف الدومين أو subdomain (مثل `clinic.yourapp.com`).
   - **مهم**: وجّه الدومين لـ `public_html/` (افتراضي) — سنرفع المشروع كامل بداخله.
3. **داتابيز MySQL**:
   - من `Databases → MySQL Databases` أنشئ قاعدة بيانات باسم `uXXXXXX_onlineclinic`.
   - أنشئ user واربطه بها مع كل الصلاحيات.
   - سجّل: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (الـ host غالباً `127.0.0.1`).
4. **إيميل SMTP**:
   - من `Emails` أنشئ إيميل على دومينك (مثل `noreply@yourapp.com`).
   - سجّل كلمة مروره — سنستخدمه في `MAIL_*`.

## الخطوة 2: رفع الكود (طريقتان)

### طريقة SSH (موصى بها — أسرع وأسلم)
من hPanel `Advanced → SSH Access` فعّل SSH إن كان متاحاً بباقتك، ثم:

```bash
cd ~
rm -rf onlineclinic
git clone https://github.com/Kerolos17/OnlineClinic.git onlineclinic
cd onlineclinic
```

### طريقة File Manager (بدون SSH)
1. على جهازك: `git archive -o onlineclinic.zip main` (يصنع zip نظيف من الكود).
2. ارفع `onlineclinic.zip` إلى `public_html/` ثم فك الضغط.
3. حرّك محتوى المشروع بحيث يكون `public_html/artisan`, `public_html/app` ... مباشرة.

## الخطوة 3: الـ .env و dependencies

```bash
cd ~/onlineclinic
cp .env.production .env
nano .env   # عدّل: APP_URL, APP_KEY (فارغ الآن), DB_*, MAIL_*, ZOOM_*
```

- `APP_KEY`: شغّل `php artisan key:generate` (ينشئ المفتاح في `.env` مباشرة).
- `APP_URL=https://YOUR_DOMAIN` بدون شُرطة مائلة زائدة.

```bash
php -d memory_limit=512M /usr/bin/composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

> لو ملوش Node على الـ server: ابنِ `public/build` محلياً (نفّذت `npm run build` هنا) وارفعه مع الكود
> — الملفات موجودة في `public/build/` ومرفوعة في git.

## الخطوة 4: التخزين والداتابيز

```bash
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force        # لو عايز بيانات تجريبية (specializations + admin + doctors)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**الصلاحيات** (مهم جداً على Shared Hosting):
```bash
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):www-data storage bootstrap/cache
```

## الخطوة 5: مجلد public (الوثيقة الرئيسية)

Laravel يدخل من `public/`. على Shared Hosting نوجه الدومين لمجلد المشروع داخل `public_html`:

- من hPanel → `Domains` → اختر الدومين → `Document Root` = `public_html/onlineclinic/public`
  (أو إن رفعت مباشرة في `public_html` فانقل محتوى `public/` إلى جذر `public_html` وعدّل `index.php` مسار `__DIR__`).

## الخطوة 6: Queue + Scheduler (إيميلات + Zoom)

Shared Hosting لا يدعم Supervisor. استخدم **Cron Jobs** من hPanel (`Advanced → Cron Jobs`):

1. **Scheduler** كل دقيقة:
   ```
   * * * * * php /home/uXXXXXX/onlineclinic/artisan schedule:run >> /dev/null 2>&1
   ```
   (الـ schedule يتضمن `zoom:create-upcoming` كل 10 دقائق).

2. **Queue worker** كل دقيقة (بديل بسيط للـ worker):
   ```
   * * * * * php /home/uXXXXXX/onlineclinic/artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
   ```
   - لو حجم العمل كبير، يمكن زيادة التردد أو تشغيل worker دائم عبر hPanel Background Jobs (إن توفر).

## الخطوة 7: SSL + ZOOM callback

1. من hPanel → `SSL` → فعّل **SSL مجاني** (Let's Encrypt) على الدومين و"Force HTTPS".
2. في حساب Zoom (marketplace app): اجعل `OAuth Redirect URL = https://YOUR_DOMAIN/zoom/callback`.
3. تأكد أن `ZOOM_REDIRECT_URI` في `.env` يطابق.

## الخطوة 8: فحص نهائي

- `php artisan about` — تأكد من production environment.
- `php artisan config:clear` بعد أي تعديل على `.env` (لا تُبقِ `config:cache` قديمة).
- افتح `https://YOUR_DOMAIN` — يجب ظهور الموقع.
- سجّل دخول للوحة المشرف `/admin` والطبيب `/doctor`.

## ملاحظات أمان مهمة

- `.env` على الخادم **مستثنى من git** — لا ترفعه أبداً، انشئه من `.env.production`.
- `APP_DEBUG=false` و `APP_ENV=production` إلزاميان (محذوفان في `.env.production`).
- لو ظهرت مشكلة مسارات (404 بعد الضغط)، شغّل `php artisan optimize` ثم أعد تجربة.