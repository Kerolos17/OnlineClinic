<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunPerformanceTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:performance {--coverage : Generate coverage report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run performance and optimization tests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Running Performance Tests...');
        $this->newLine();

        // Run performance tests
        $this->info('📊 Running Performance Test Suite...');
        $exitCode = Artisan::call('test', [
            '--filter' => 'PerformanceTest',
            '--stop-on-failure' => true,
        ]);

        if ($exitCode !== 0) {
            $this->error('❌ Performance tests failed!');

            return 1;
        }

        $this->info('✅ Performance tests passed!');
        $this->newLine();

        // Run booking flow tests
        $this->info('🎫 Running Booking Flow Tests...');
        $exitCode = Artisan::call('test', [
            '--filter' => 'BookingFlowTest',
            '--stop-on-failure' => true,
        ]);

        if ($exitCode !== 0) {
            $this->error('❌ Booking flow tests failed!');

            return 1;
        }

        $this->info('✅ Booking flow tests passed!');
        $this->newLine();

        // Run doctor listing tests
        $this->info('👨‍⚕️ Running Doctor Listing Tests...');
        $exitCode = Artisan::call('test', [
            '--filter' => 'DoctorListingTest',
            '--stop-on-failure' => true,
        ]);

        if ($exitCode !== 0) {
            $this->error('❌ Doctor listing tests failed!');

            return 1;
        }

        $this->info('✅ Doctor listing tests passed!');
        $this->newLine();

        // Run unit tests
        $this->info('🔬 Running Unit Tests...');
        $exitCode = Artisan::call('test', [
            '--testsuite' => 'Unit',
            '--stop-on-failure' => true,
        ]);

        if ($exitCode !== 0) {
            $this->error('❌ Unit tests failed!');

            return 1;
        }

        $this->info('✅ Unit tests passed!');
        $this->newLine();

        // Generate coverage if requested
        if ($this->option('coverage')) {
            $this->info('📈 Generating Coverage Report...');
            Artisan::call('test', [
                '--coverage' => true,
                '--min' => '80',
            ]);
        }

        $this->newLine();
        $this->info('🎉 All tests passed successfully!');

        return 0;
    }
}
