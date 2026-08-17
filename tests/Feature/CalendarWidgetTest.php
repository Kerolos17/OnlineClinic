<?php

use App\Filament\Doctor\Widgets\CalendarWidget;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user   = User::factory()->create(['role' => 'doctor']);
    $this->doctor = Doctor::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('can render calendar widget', function () {
    Livewire::test(CalendarWidget::class)
        ->assertSuccessful()
        ->assertSee('Legend'); // Using a simpler text that should be visible
});

it('can set appointment type filter', function () {
    Livewire::test(CalendarWidget::class)
        ->call('setAppointmentTypeFilter', 'online')
        ->assertSet('appointmentTypeFilter', 'online');
});

it('can set date range preset', function () {
    Livewire::test(CalendarWidget::class)
        ->call('setDateRangePreset', 'this_week')
        ->assertSet('dateRangePreset', 'this_week')
        ->assertSet('dateRangeStart', function ($value) {
            return $value !== null;
        })
        ->assertSet('dateRangeEnd', function ($value) {
            return $value !== null;
        });
});

it('can toggle mobile view', function () {
    Livewire::test(CalendarWidget::class)
        ->call('toggleMobileView')
        ->assertSet('showMobileView', true);
});

it('can navigate months', function () {
    $currentMonth = now()->month;
    $currentYear  = now()->year;

    Livewire::test(CalendarWidget::class)
        ->call('nextMonth')
        ->assertSet('currentMonth', $currentMonth === 12 ? 1 : $currentMonth + 1);
});

it('can clear date range filters', function () {
    Livewire::test(CalendarWidget::class)
        ->call('setDateRangePreset', 'this_week')
        ->call('clearDateRange')
        ->assertSet('dateRangePreset', 'current_month')
        ->assertSet('customStartDate', function ($value) {
            return $value === null;
        })
        ->assertSet('customEndDate', function ($value) {
            return $value === null;
        });
});

it('provides correct month options', function () {
    $widget = new CalendarWidget();
    $months = $widget->getMonthOptions();

    expect($months)->toHaveCount(12)
        ->and($months[1])->toBe('January')
        ->and($months[12])->toBe('December');
});

it('provides correct date range presets', function () {
    $widget  = new CalendarWidget();
    $presets = $widget->getDateRangePresets();

    expect($presets)->toHaveKey('current_month')
        ->and($presets)->toHaveKey('this_week')
        ->and($presets)->toHaveKey('custom');
});
