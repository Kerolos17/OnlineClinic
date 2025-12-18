# Redis Setup Guide for WellClinic

## Why Redis?

Redis يوفر:

-   **Cache Tagging**: إمكانية مسح مجموعات من الـ cache بسهولة
-   **Performance**: أسرع من database/file cache
-   **Queue Driver**: أفضل من database للـ queues
-   **Session Storage**: أسرع وأكثر كفاءة

## Installation

### Windows (XAMPP)

#### Option 1: Using WSL2 (Recommended)

```bash
# Install WSL2
wsl --install

# Inside WSL, install Redis
sudo apt update
sudo apt install redis-server

# Start Redis
sudo service redis-server start

# Test
redis-cli ping
# Should return: PONG
```

#### Option 2: Using Memurai (Redis for Windows)

1. Download from: https://www.memurai.com/
2. Install and start the service
3. Default port: 6379

### Linux/Mac

```bash
# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl start redis

# Mac
brew install redis
brew services start redis
```

## Laravel Configuration

### 1. Install PHP Redis Extension

```bash
# Check if already installed
php -m | grep redis

# If not installed:
# Windows: Enable in php.ini
extension=redis

# Linux
sudo apt install php-redis

# Restart web server
```

### 2. Install Predis (Alternative)

```bash
composer require predis/predis
```

### 3. Update .env

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_CLIENT=phpredis  # or predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Update config/cache.php

```php
'default' => env('CACHE_STORE', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

### 5. Update config/database.php

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
],
```

## Testing Redis

```bash
# Test connection
php artisan tinker
>>> Cache::put('test', 'Hello Redis!', 60);
>>> Cache::get('test');
# Should return: "Hello Redis!"

# Test tags (only works with Redis)
>>> Cache::tags(['doctors'])->put('test', 'Tagged cache', 60);
>>> Cache::tags(['doctors'])->get('test');
>>> Cache::tags(['doctors'])->flush();
```

## Enable Cache Tags in Code

Once Redis is setup, uncomment the tag-based caching in:

### app/Observers/DoctorObserver.php

```php
private function clearCache(Doctor $doctor): void
{
    // Use tags with Redis
    Cache::tags(['doctors', "doctor:{$doctor->id}"])->flush();
}
```

### app/Observers/SlotObserver.php

```php
private function clearCache(Slot $slot): void
{
    Cache::tags(["slots:doctor:{$slot->doctor_id}"])->flush();
}
```

### app/Observers/SpecializationObserver.php

```php
private function clearCache(): void
{
    Cache::tags(['specializations'])->flush();
}
```

### app/Http/Controllers/Api/DoctorController.php

```php
// Use tags in all Cache::remember() calls
$doctors = Cache::tags(['doctors'])->remember($cacheKey, 300, function () {
    // ...
});
```

## Monitoring Redis

### Redis CLI Commands

```bash
# Connect to Redis
redis-cli

# Monitor all commands
MONITOR

# Get all keys
KEYS *

# Get cache statistics
INFO stats

# Clear all cache
FLUSHDB

# Clear specific pattern
redis-cli KEYS "wellclinic*" | xargs redis-cli DEL
```

### Laravel Commands

```bash
# Clear cache
php artisan cache:clear

# Clear specific tags
php artisan tinker
>>> Cache::tags(['doctors'])->flush();
```

## Production Considerations

### 1. Redis Persistence

```bash
# Edit redis.conf
save 900 1      # Save after 900 seconds if 1 key changed
save 300 10     # Save after 300 seconds if 10 keys changed
save 60 10000   # Save after 60 seconds if 10000 keys changed
```

### 2. Memory Management

```bash
# Set max memory
maxmemory 256mb
maxmemory-policy allkeys-lru  # Remove least recently used keys
```

### 3. Security

```bash
# Set password
requirepass your-strong-password

# Update .env
REDIS_PASSWORD=your-strong-password
```

### 4. Monitoring Tools

-   **Redis Commander**: Web-based Redis management
-   **RedisInsight**: Official Redis GUI
-   **Laravel Telescope**: Monitor cache operations

## Troubleshooting

### Connection Refused

```bash
# Check if Redis is running
sudo service redis-server status

# Check port
netstat -an | grep 6379

# Check logs
tail -f /var/log/redis/redis-server.log
```

### Permission Denied

```bash
# Fix Redis socket permissions
sudo chmod 777 /var/run/redis/redis.sock
```

### Slow Performance

```bash
# Check slow log
redis-cli SLOWLOG GET 10

# Monitor memory
redis-cli INFO memory
```

## Benefits After Setup

✅ **Faster Cache**: 10-100x faster than database cache
✅ **Cache Tags**: Easy cache invalidation
✅ **Better Queues**: Faster job processing
✅ **Session Storage**: Improved user experience
✅ **Real-time Features**: Foundation for WebSockets/Broadcasting

## Next Steps

After Redis is working:

1. Enable cache tags in all observers
2. Update queue driver to Redis
3. Consider using Redis for sessions
4. Setup Laravel Horizon for queue monitoring
5. Implement cache warming strategies
