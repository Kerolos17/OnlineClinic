<?php
namespace App\Filament\Doctor\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class CalendarWidget extends Widget
{
    protected static string $view = 'filament.doctor.widgets.calendar-widget';

    protected int|string|array $columnSpan = 'full';

    public $currentMonth;
    public $currentYear;
    public $selectedDate          = null;
    public $appointmentTypeFilter = 'all';
    public $dateRangeStart        = null;
    public $dateRangeEnd          = null;
    public $showMobileView        = false;
    public $dateRangePreset       = 'current_month';
    public $showDateRangePicker   = false;
    public $customStartDate       = null;
    public $customEndDate         = null;

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear  = now()->year;
        $this->applyDateRangePreset($this->dateRangePreset);
    }

    public function getHeading(): string
    {
        return __('doctor.calendar.title');
    }

    public function previousMonth(): void
    {
        $date               = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear  = $date->year;
    }

    public function nextMonth(): void
    {
        $date               = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear  = $date->year;
    }

    public function goToToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear  = now()->year;
    }

    public function selectDate($date): void
    {
        $this->selectedDate = $date;
        $this->dispatch('open-modal', id: 'create-appointment-modal');
    }

    public function setAppointmentTypeFilter($type): void
    {
        $this->appointmentTypeFilter = $type;
    }

    public function setDateRange($start, $end): void
    {
        $this->dateRangeStart = $start;
        $this->dateRangeEnd   = $end;
    }

    public function clearDateRange(): void
    {
        $this->dateRangeStart      = null;
        $this->dateRangeEnd        = null;
        $this->dateRangePreset     = 'current_month';
        $this->customStartDate     = null;
        $this->customEndDate       = null;
        $this->showDateRangePicker = false;
        $this->applyDateRangePreset('current_month');
    }

    public function toggleMobileView(): void
    {
        $this->showMobileView = ! $this->showMobileView;
    }

    public function goToMonth($month, $year): void
    {
        $this->currentMonth = $month;
        $this->currentYear  = $year;
    }

    public function setDateRangePreset($preset): void
    {
        $this->dateRangePreset = $preset;
        $this->applyDateRangePreset($preset);
        $this->showDateRangePicker = false;
    }

    public function applyDateRangePreset($preset): void
    {
        switch ($preset) {
            case 'this_week':
                $this->dateRangeStart = now()->startOfWeek()->toDateString();
                $this->dateRangeEnd   = now()->endOfWeek()->toDateString();
                break;
            case 'next_week':
                $this->dateRangeStart = now()->addWeek()->startOfWeek()->toDateString();
                $this->dateRangeEnd   = now()->addWeek()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->dateRangeStart = now()->startOfMonth()->toDateString();
                $this->dateRangeEnd   = now()->endOfMonth()->toDateString();
                break;
            case 'next_month':
                $this->dateRangeStart = now()->addMonth()->startOfMonth()->toDateString();
                $this->dateRangeEnd   = now()->addMonth()->endOfMonth()->toDateString();
                break;
            case 'next_3_months':
                $this->dateRangeStart = now()->startOfMonth()->toDateString();
                $this->dateRangeEnd   = now()->addMonths(3)->endOfMonth()->toDateString();
                break;
            case 'custom':
                $this->showDateRangePicker = true;
                if ($this->customStartDate && $this->customEndDate) {
                    $this->dateRangeStart = $this->customStartDate;
                    $this->dateRangeEnd   = $this->customEndDate;
                }
                break;
            default: // current_month
                $this->dateRangeStart = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth()->toDateString();
                $this->dateRangeEnd   = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth()->toDateString();
                break;
        }
    }

    public function setCustomDateRange(): void
    {
        if ($this->customStartDate && $this->customEndDate) {
            $this->dateRangeStart  = $this->customStartDate;
            $this->dateRangeEnd    = $this->customEndDate;
            $this->dateRangePreset = 'custom';
        }
    }

    public function goToYear($year): void
    {
        $this->currentYear = $year;
        if ($this->dateRangePreset === 'current_month') {
            $this->applyDateRangePreset('current_month');
        }
    }

    public function goToSpecificMonth($month): void
    {
        $this->currentMonth = $month;
        if ($this->dateRangePreset === 'current_month') {
            $this->applyDateRangePreset('current_month');
        }
    }

    public function toggleDateRangePicker(): void
    {
        $this->showDateRangePicker = ! $this->showDateRangePicker;
        if ($this->showDateRangePicker) {
            $this->dateRangePreset = 'custom';
        }
    }

    public function getAvailableYears(): array
    {
        $currentYear = now()->year;
        $years       = [];
        for ($i = $currentYear - 1; $i <= $currentYear + 2; $i++) {
            $years[] = $i;
        }
        return $years;
    }

    public function getMonthOptions(): array
    {
        return [
            1  => __('doctor.calendar.months.january'),
            2  => __('doctor.calendar.months.february'),
            3  => __('doctor.calendar.months.march'),
            4  => __('doctor.calendar.months.april'),
            5  => __('doctor.calendar.months.may'),
            6  => __('doctor.calendar.months.june'),
            7  => __('doctor.calendar.months.july'),
            8  => __('doctor.calendar.months.august'),
            9  => __('doctor.calendar.months.september'),
            10 => __('doctor.calendar.months.october'),
            11 => __('doctor.calendar.months.november'),
            12 => __('doctor.calendar.months.december'),
        ];
    }

    public function getDateRangePresets(): array
    {
        return [
            'current_month' => __('doctor.calendar.presets.current_month'),
            'this_week'     => __('doctor.calendar.presets.this_week'),
            'next_week'     => __('doctor.calendar.presets.next_week'),
            'this_month'    => __('doctor.calendar.presets.this_month'),
            'next_month'    => __('doctor.calendar.presets.next_month'),
            'next_3_months' => __('doctor.calendar.presets.next_3_months'),
            'custom'        => __('doctor.calendar.presets.custom'),
        ];
    }

    public function getCalendarData(): array
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return [];
        }

        // Determine date range
        if ($this->dateRangeStart && $this->dateRangeEnd) {
            $startDate = Carbon::parse($this->dateRangeStart);
            $endDate   = Carbon::parse($this->dateRangeEnd);
        } else {
            $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
        }

        // Get all slots for the date range
        $slotsQuery = $doctor->slots()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('booking');

        // Apply appointment type filter
        if ($this->appointmentTypeFilter !== 'all') {
            $slotsQuery->where('type', $this->appointmentTypeFilter);
        }

        $slots = $slotsQuery->get();

        // Group slots by date
        $calendarData = [];
        foreach ($slots as $slot) {
            $date = $slot->date->format('Y-m-d');
            if (! isset($calendarData[$date])) {
                $calendarData[$date] = [
                    'total'     => 0,
                    'online'    => 0,
                    'clinic'    => 0,
                    'booked'    => 0,
                    'available' => 0,
                ];
            }

            $calendarData[$date]['total']++;
            $calendarData[$date][$slot->type]++;
            $calendarData[$date][$slot->status]++;
        }

        return $calendarData;
    }

    public function getMonthName(): string
    {
        return Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y');
    }

    public function getCalendarDays(): array
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        // Start from the beginning of the week
        $calendarStart = $startDate->copy()->startOfWeek();
        $calendarEnd   = $endDate->copy()->endOfWeek();

        $days    = [];
        $current = $calendarStart->copy();

        while ($current <= $calendarEnd) {
            $days[] = [
                'date'           => $current->copy(),
                'isCurrentMonth' => $current->month === $this->currentMonth,
                'isToday'        => $current->isToday(),
                'isPast'         => $current->isPast(),
            ];
            $current->addDay();
        }

        return $days;
    }

    public function createAppointmentSlot(): void
    {
        if (! $this->selectedDate) {
            return;
        }

        $this->dispatch('redirect', url: route('filament.doctor.resources.appointments.create', [
            'date' => $this->selectedDate,
        ]));
    }

    public function createOnlineAppointment(): void
    {
        if (! $this->selectedDate) {
            return;
        }

        $this->dispatch('redirect', url: route('filament.doctor.resources.appointments.create', [
            'date' => $this->selectedDate,
            'type' => 'online',
        ]));
    }

    public function createClinicAppointment(): void
    {
        if (! $this->selectedDate) {
            return;
        }

        $this->dispatch('redirect', url: route('filament.doctor.resources.appointments.create', [
            'date' => $this->selectedDate,
            'type' => 'clinic',
        ]));
    }

    public function getDensityLevel($dayData): string
    {
        if (! $dayData) {
            return 'none';
        }

        $total = $dayData['total'];
        if ($total === 0) {
            return 'none';
        } elseif ($total <= 2) {
            return 'low';
        } elseif ($total <= 5) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    public function getDensityColor($level): string
    {
        return match ($level) {
            'none'   => 'bg-gray-50 dark:bg-gray-800',
            'low'    => 'bg-blue-50 dark:bg-blue-900/20',
            'medium' => 'bg-blue-100 dark:bg-blue-800/30',
            'high'   => 'bg-blue-200 dark:bg-blue-700/40',
            default  => 'bg-gray-50 dark:bg-gray-800',
        };
    }

    public function getAppointmentStats(): array
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return [
                'total_slots'         => 0,
                'booked_slots'        => 0,
                'available_slots'     => 0,
                'online_appointments' => 0,
                'clinic_appointments' => 0,
            ];
        }

        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $slots = $doctor->slots()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        return [
            'total_slots'         => $slots->count(),
            'booked_slots'        => $slots->where('status', 'booked')->count(),
            'available_slots'     => $slots->where('status', 'available')->count(),
            'online_appointments' => $slots->where('type', 'online')->count(),
            'clinic_appointments' => $slots->where('type', 'clinic')->count(),
        ];
    }
}
