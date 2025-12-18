# Project Structure

## Root Directory

-   `.env` - Environment configuration (database, API keys)
-   `composer.json` - PHP dependencies and custom scripts
-   `package.json` - JavaScript dependencies
-   `artisan` - Laravel CLI tool
-   `vite.config.js` - Frontend build configuration

## Application Code (`app/`)

### Models (`app/Models/`)

Core Eloquent models representing database entities:

-   `User.php` - System users (admin, doctor, patient)
-   `Doctor.php` - Doctor profiles and information
-   `Specialization.php` - Medical specializations
-   `DoctorAvailability.php` - Weekly availability schedules
-   `Slot.php` - Individual bookable time slots
-   `Booking.php` - Patient appointments
-   `Payment.php` - Payment transactions

### HTTP Layer (`app/Http/`)

-   `Controllers/` - Request handling and business logic
-   `Middleware/` - Request filtering and modification

### Admin Panel (`app/Filament/`)

-   `Resources/` - Filament CRUD resources for admin panel
-   `Widgets/` - Dashboard widgets (stats, charts)

### Background Jobs (`app/Jobs/`)

-   `CreateZoomMeeting.php` - Async Zoom meeting creation

### Service Providers (`app/Providers/`)

-   `AppServiceProvider.php` - Application service bindings
-   `Filament/` - Filament-specific providers

## Configuration (`config/`)

Laravel configuration files for app, database, cache, queue, mail, etc.

## Database (`database/`)

-   `migrations/` - Database schema definitions
-   `seeders/` - Sample data generators
-   `factories/` - Model factories for testing

## Frontend (`resources/`)

-   `views/` - Blade templates
-   `css/` - Stylesheets (Tailwind)
-   `js/` - JavaScript files

## Public Assets (`public/`)

-   `index.php` - Application entry point
-   `build/` - Compiled Vite assets
-   `css/`, `js/` - Static assets

## Localization (`lang/`)

-   `en/` - English translations
-   `ar/` - Arabic translations

## Routes (`routes/`)

-   `web.php` - Web routes
-   `api.php` - API routes (if exists)

## Testing (`tests/`)

-   `Feature/` - Feature tests
-   `Unit/` - Unit tests
-   `Pest.php` - Pest configuration

## Storage (`storage/`)

-   `app/` - Application files (uploads, etc.)
-   `framework/` - Framework cache and sessions
-   `logs/` - Application logs

## Key Patterns

-   **Multi-language**: JSON columns store translations (`{"en": "...", "ar": "..."}`)
-   **Enum fields**: Status fields use predefined values (pending, confirmed, completed, cancelled)
-   **Cascade deletes**: Related records deleted automatically when parent is removed
-   **Filament resources**: Admin CRUD follows Filament conventions
-   **Queue jobs**: Long-running tasks (Zoom API) processed asynchronously
