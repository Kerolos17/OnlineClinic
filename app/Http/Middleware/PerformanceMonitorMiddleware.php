<?php

namespace App\Http\Middleware;

use App\Services\PerformanceMonitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start monitoring
        PerformanceMonitor::start();

        // Process request
        $response = $next($request);

        // Stop monitoring and get stats
        $stats = PerformanceMonitor::stop();

        // Log performance for slow requests (>500ms)
        $totalTime = (float) str_replace('ms', '', $stats['total_time']);
        if ($totalTime > 500) {
            Log::warning('Slow Request Detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'stats' => $stats,
            ]);
        }

        // Add performance headers in development
        if (app()->environment('local')) {
            $response->headers->set('X-Query-Count', $stats['query_count']);
            $response->headers->set('X-Query-Time', $stats['query_time']);
            $response->headers->set('X-Total-Time', $stats['total_time']);
            $response->headers->set('X-Memory-Usage', $stats['memory_usage']);
        }

        // Check for N+1 queries
        if (PerformanceMonitor::detectNPlusOne(15)) {
            Log::warning('Possible N+1 Query Problem', [
                'url' => $request->fullUrl(),
                'query_count' => $stats['query_count'],
            ]);
        }

        return $response;
    }
}
