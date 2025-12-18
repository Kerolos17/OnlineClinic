# WellClinic - Telemedicine Platform

A bilingual (English/Arabic) telemedicine platform built with Laravel 12 and Filament 3.3 that connects patients with doctors for online consultations via Zoom.

## Features

-   🏥 **Doctor Management**: Complete doctor profiles with specializations and availability
-   📅 **Appointment Booking**: Real-time slot-based booking system
-   💳 **Payment Integration**: Support for Stripe, Paymob, and Cash payments
-   🎥 **Zoom Integration**: Automatic Zoom meeting creation for consultations
-   📧 **Email Notifications**: Automated booking confirmations and reminders
-   🌐 **Bilingual Support**: Full English and Arabic support with RTL
-   🎨 **Admin Panel**: Powerful Filament-based admin interface

## Requirements

-   PHP 8.2+
-   MySQL/MariaDB
-   Composer
-   Node.js & npm
-   Zoom Account (for video consultations)

## Quick Start

📖 **New to the project?** Check out the [Quick Start Guide](QUICK_START.md) for a simplified setup process.

### 1. Installation

```bash
# Clone the repository
git clone <repository-url>
cd wellclinic

# Install dependencies and setup
composer setup
```

This command will:

-   Install PHP dependencies
-   Copy `.env.example` to `.env`
-   Generate application key
-   Run database migrations
-   Install and build frontend assets

### 2. Configuration

Edit `.env` file and configure:

```env
# Database
DB_DATABASE=wellclinic
DB_USERNAME=root
DB_PASSWORD=

# Mail (Gmail example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com

# Zoom API
ZOOM_ACCOUNT_ID=your-account-id
ZOOM_CLIENT_ID=your-client-id
ZOOM_CLIENT_SECRET=your-client-secret

# Queue (important!)
QUEUE_CONNECTION=database
```

### 3. Run Development Server

```bash
composer dev
```

This command runs:

-   Laravel development server (http://127.0.0.1:8000)
-   Queue worker (for background jobs)
-   Vite dev server (for hot module replacement)

**⚠️ Important**: The queue worker MUST be running for Zoom meetings and emails to work!

## Important: Queue Worker

The application uses Laravel queues for:

-   Creating Zoom meetings
-   Sending email notifications
-   Processing background tasks

### Development

Use the provided composer script:

```bash
composer dev
```

### Production

Use Supervisor to keep the queue worker running:

```ini
[program:wellclinic-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/wellclinic/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/wellclinic/storage/logs/worker.log
```

### Manual Processing

If you need to manually process pending Zoom meetings:

```bash
php process-pending-zoom.php
```

## Scheduled Tasks

The application has scheduled tasks that run every 10 minutes to create Zoom meetings for upcoming appointments.

### Setup (Production)

Add to crontab:

```bash
* * * * * cd /path/to/wellclinic && php artisan schedule:run >> /dev/null 2>&1
```

### Windows (XAMPP)

Use Task Scheduler to run every minute:

```
C:\xampp\php\php.exe C:\xampp\htdocs\WellClinic\artisan schedule:run
```

## Admin Panel

Access the admin panel at: `http://127.0.0.1:8000/admin`

Create an admin user:

```bash
php artisan tinker
>>> $user = \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
>>> $user->assignRole('admin');
```

## Testing

```bash
composer test
```

## Code Style

Format code using Laravel Pint:

```bash
./vendor/bin/pint
```

## System Health Check

Run a quick health check to verify everything is configured correctly:

```bash
php check-system-health.php
```

This will check:

-   Database connection
-   Queue configuration
-   Zoom API configuration
-   Mail configuration
-   Pending bookings status

## Troubleshooting

### Zoom meetings not being created?

1. Run health check: `php check-system-health.php`
2. Check if queue worker is running: `composer dev`
3. Check failed jobs: `php artisan queue:failed`
4. Manually process pending bookings: `php process-pending-zoom.php`
5. See detailed guide: [ZOOM_FIX_GUIDE.md](ZOOM_FIX_GUIDE.md)

### Emails not being sent?

1. Verify mail configuration in `.env`
2. Check if queue worker is running
3. Test email: `php test-emails.php`

## Documentation

-   [Zoom System Flow](ZOOM_SYSTEM_FLOW.md)
-   [Zoom Fix Guide](ZOOM_FIX_GUIDE.md)
-   [Email Setup](EMAIL_SETUP.md)
-   [Booking Resource Fix](BOOKING_RESOURCE_FIX.md)
-   [Notifications (English Only)](NOTIFICATIONS_ENGLISH_ONLY.md)

## Technology Stack

-   **Backend**: Laravel 12, Filament 3.3
-   **Frontend**: Tailwind CSS 4.0, Vite 7.0
-   **Database**: MySQL/MariaDB
-   **Testing**: Pest 4.1
-   **Code Quality**: Laravel Pint (PSR-12)

## License

This project is proprietary software.
