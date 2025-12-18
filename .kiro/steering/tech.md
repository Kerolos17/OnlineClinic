# Technology Stack

## Backend

-   **Framework**: Laravel 12 (PHP 8.2+)
-   **Admin Panel**: Filament 3.3
-   **Localization**: mcamara/laravel-localization 2.3
-   **Testing**: Pest 4.1 with Laravel plugin
-   **Code Quality**: Laravel Pint (PSR-12 style)

## Frontend

-   **CSS Framework**: Tailwind CSS 4.0
-   **Build Tool**: Vite 7.0
-   **JavaScript**: Axios for HTTP requests

## Database

-   MySQL/MariaDB with 9 main tables
-   JSON columns for multi-language content (name, description, bio)
-   Queue jobs table for background processing

## Development Tools

-   **Package Manager**: Composer (PHP), npm (JavaScript)
-   **Local Development**: Laravel Sail (Docker-based)
-   **Logging**: Laravel Pail for real-time log viewing

## Common Commands

### Setup

```bash
composer setup
# Runs: composer install, .env setup, key generation, migrations, npm install & build
```

### Development

```bash
composer dev
# Runs concurrently: php artisan serve, queue listener, npm run dev
```

### Testing

```bash
composer test
# Runs: config:clear, artisan test (Pest)
```

### Code Style

```bash
./vendor/bin/pint
# Formats code to PSR-12 standards
```

### Database

```bash
php artisan migrate
php artisan db:seed
```

### Queue Processing

```bash
php artisan queue:work
# Process background jobs (Zoom meeting creation, etc.)
```

## File Structure Conventions

-   Models use Eloquent ORM with relationships
-   Controllers follow Laravel conventions
-   Filament resources in `app/Filament/Resources/`
-   Background jobs in `app/Jobs/`
-   Migrations use descriptive names with timestamps
