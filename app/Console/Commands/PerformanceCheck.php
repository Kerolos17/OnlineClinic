<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use App\Services\PerformanceMonitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PerformanceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:check {--warmup : Warm up cache after checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run performance checks and optimization tests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Performance Checks...');
        $this->newLine();

        // 1. Database Connection Check
        $this->checkDatabaseConnection();

        // 2. Query Performance Check
        $this->checkQueryPerformance();

        // 3. Cache Check
        $this->checkCache();

        // 4. Memory Usage Check
        $this->checkMemoryUsage();

        // 5. Run Tests
        if ($this->confirm('Run automated tests?', true)) {
            $this->runTests();
        }

        // 6. Warm up cache if requested
        if ($this->option('warmup')) {
            $this->warmUpCache();
        }

        $this->newLine();
        $this->info('✅ Performance checks completed!');
    }

    protected function checkDatabaseConnection()
    {
        $this->info('📊 Checking database connection...');

        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $time = (microtime(true) - $start) * 1000;

            $this->line('   ✓ Database connected in '.round($time, 2).'ms');
        } catch (\Exception $e) {
            $this->error('   ✗ Database connection failed: '.$e->getMessage());
        }

        $this->newLine();
    }

    protected function checkQueryPerformance()
    {
        $this->info('⚡ Checking query performance...');

        PerformanceMonitor::start();

        // Test query: Get all active doctors with relations
        $doctors = \App\Models\Doctor::active()
            ->withRelations()
            ->limit(10)
            ->get();

        $stats = PerformanceMonitor::stop();

        $this->line("   ✓ Loaded {$doctors->count()} doctors");
        $this->line("   • Query count: {$stats['query_count']}");
        $this->line("   • Query time: {$stats['query_time']}");
        $this->line("   • Total time: {$stats['total_time']}");

        if ($stats['query_count'] > 10) {
            $this->warn('   ⚠ High query count detected! Consider eager loading.');
        }

        $this->newLine();
    }

    protected function checkCache()
    {
        $this->info('💾 Checking cache system...');

        try {
            // Test cache write
            $start = microtime(true);
            cache()->put('performance_test', 'test_value', 60);
            $writeTime = (microtime(true) - $start) * 1000;

            // Test cache read
            $start = microtime(true);
            $value = cache()->get('performance_test');
            $readTime = (microtime(true) - $start) * 1000;

            // Clean up
            cache()->forget('performance_test');

            $this->line('   ✓ Cache write: '.round($writeTime, 2).'ms');
            $this->line('   ✓ Cache read: '.round($readTime, 2).'ms');

            if ($writeTime > 10 || $readTime > 10) {
                $this->warn('   ⚠ Cache operations are slow. Consider using Redis.');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Cache check failed: '.$e->getMessage());
        }

        $this->newLine();
    }

    protected function checkMemoryUsage()
    {
        $this->info('💻 Checking memory usage...');

        $memory = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);

        $this->line('   • Current: '.$this->formatBytes($memory));
        $this->line('   • Peak: '.$this->formatBytes($peak));

        if ($peak > 128 * 1024 * 1024) { // 128MB
            $this->warn('   ⚠ High memory usage detected!');
        } else {
            $this->line('   ✓ Memory usage is normal');
        }

        $this->newLine();
    }

    protected function runTests()
    {
        $this->info('🧪 Running automated tests...');
        $this->newLine();

        // Run tests
        $exitCode = Artisan::call('test', [
            '--testsuite' => 'Feature',
            '--stop-on-failure' => true,
        ]);

        if ($exitCode === 0) {
            $this->info('   ✓ All tests passed!');
        } else {
            $this->error('   ✗ Some tests failed!');
        }

        $this->newLine();
    }

    protected function warmUpCache()
    {
        $this->info('🔥 Warming up cache...');

        $start = microtime(true);
        CacheService::warmUp();
        $time = (microtime(true) - $start) * 1000;

        $this->line('   ✓ Cache warmed up in '.round($time, 2).'ms');
        $this->newLine();
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
