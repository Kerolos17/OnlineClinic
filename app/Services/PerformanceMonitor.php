<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    protected static $queryCount = 0;

    protected static $queryTime = 0;

    protected static $startTime = null;

    /**
     * Start monitoring
     */
    public static function start()
    {
        self::$startTime = microtime(true);
        self::$queryCount = 0;
        self::$queryTime = 0;

        // Listen to database queries
        DB::listen(function ($query) {
            self::$queryCount++;
            self::$queryTime += $query->time;

            // Log slow queries (> 100ms)
            if ($query->time > 100) {
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time.'ms',
                ]);
            }
        });
    }

    /**
     * Stop monitoring and return stats
     */
    public static function stop()
    {
        $endTime = microtime(true);
        $totalTime = ($endTime - self::$startTime) * 1000; // Convert to ms

        return [
            'total_time' => round($totalTime, 2).'ms',
            'query_count' => self::$queryCount,
            'query_time' => round(self::$queryTime, 2).'ms',
            'memory_usage' => self::formatBytes(memory_get_peak_usage(true)),
        ];
    }

    /**
     * Get current stats without stopping
     */
    public static function getStats()
    {
        $currentTime = microtime(true);
        $totalTime = ($currentTime - self::$startTime) * 1000;

        return [
            'elapsed_time' => round($totalTime, 2).'ms',
            'query_count' => self::$queryCount,
            'query_time' => round(self::$queryTime, 2).'ms',
            'memory_usage' => self::formatBytes(memory_get_usage(true)),
        ];
    }

    /**
     * Log performance stats
     */
    public static function log($label = 'Performance')
    {
        $stats = self::getStats();

        Log::info($label, $stats);

        // Alert if performance is poor
        if (self::$queryCount > 50) {
            Log::warning('High Query Count', [
                'count' => self::$queryCount,
                'label' => $label,
            ]);
        }

        if (self::$queryTime > 1000) {
            Log::warning('High Query Time', [
                'time' => self::$queryTime.'ms',
                'label' => $label,
            ]);
        }
    }

    /**
     * Format bytes to human readable
     */
    protected static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    /**
     * Check if N+1 query problem exists
     */
    public static function detectNPlusOne($threshold = 10)
    {
        if (self::$queryCount > $threshold) {
            Log::warning('Possible N+1 Query Problem', [
                'query_count' => self::$queryCount,
                'threshold' => $threshold,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Measure execution time of a callback
     */
    public static function measure(callable $callback, $label = 'Operation')
    {
        $start = microtime(true);
        $startQueries = self::$queryCount;

        $result = $callback();

        $end = microtime(true);
        $time = ($end - $start) * 1000;
        $queries = self::$queryCount - $startQueries;

        Log::info("Performance: {$label}", [
            'time' => round($time, 2).'ms',
            'queries' => $queries,
        ]);

        return $result;
    }
}
