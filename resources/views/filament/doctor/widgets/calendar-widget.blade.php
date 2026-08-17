<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Enhanced Calendar Header --}}
            <div class="space-y-4">
                {{-- Top Row: Title and Enhanced Navigation --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $this->getMonthName() }}
                        </h3>

                        {{-- Enhanced Navigation with Dropdowns --}}
                        <div
                            class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                            <x-filament::button wire:click="previousMonth" size="sm" color="gray"
                                icon="heroicon-m-chevron-left"
                                class="{{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" />

                            {{-- Month Selector --}}
                            <div class="relative">
                                <select wire:model.live="currentMonth"
                                    wire:change="goToSpecificMonth($event.target.value)"
                                    class="text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                    @foreach ($this->getMonthOptions() as $monthNum => $monthName)
                                        <option value="{{ $monthNum }}">{{ $monthName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Year Selector --}}
                            <div class="relative">
                                <select wire:model.live="currentYear" wire:change="goToYear($event.target.value)"
                                    class="text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                    @foreach ($this->getAvailableYears() as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <x-filament::button wire:click="goToToday" size="sm" color="primary">
                                {{ __('doctor.calendar.today') }}
                            </x-filament::button>

                            <x-filament::button wire:click="nextMonth" size="sm" color="gray"
                                icon="heroicon-m-chevron-right"
                                class="{{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" />
                        </div>
                    </div>

                    {{-- Mobile View Toggle --}}
                    <div class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <x-filament::button wire:click="toggleMobileView" size="sm" color="gray"
                            class="lg:hidden">
                            <x-heroicon-m-device-phone-mobile
                                class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                            {{ $showMobileView ? __('doctor.calendar.desktop_view') : __('doctor.calendar.mobile_view') }}
                        </x-filament::button>
                    </div>
                </div>

                {{-- Second Row: Filters and Date Range Controls --}}
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                    {{-- Date Range Presets --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('doctor.calendar.date_range') }}:
                        </span>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($this->getDateRangePresets() as $preset => $label)
                                <x-filament::button wire:click="setDateRangePreset('{{ $preset }}')"
                                    size="sm" :color="$dateRangePreset === $preset ? 'primary' : 'gray'">
                                    {{ $label }}
                                </x-filament::button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Appointment Type Filter --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('doctor.calendar.filter_by_type') }}:
                        </span>

                        <x-filament::button.group>
                            <x-filament::button wire:click="setAppointmentTypeFilter('all')" size="sm"
                                :color="$appointmentTypeFilter === 'all' ? 'primary' : 'gray'">
                                {{ __('doctor.calendar.all') }}
                            </x-filament::button>

                            <x-filament::button wire:click="setAppointmentTypeFilter('online')" size="sm"
                                :color="$appointmentTypeFilter === 'online' ? 'success' : 'gray'">
                                <x-heroicon-m-video-camera
                                    class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                                {{ __('doctor.appointment_types.online') }}
                            </x-filament::button>

                            <x-filament::button wire:click="setAppointmentTypeFilter('clinic')" size="sm"
                                :color="$appointmentTypeFilter === 'clinic' ? 'info' : 'gray'">
                                <x-heroicon-m-building-office-2
                                    class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                                {{ __('doctor.appointment_types.clinic') }}
                            </x-filament::button>
                        </x-filament::button.group>
                    </div>
                </div>

                {{-- Custom Date Range Picker (shown when custom preset is selected) --}}
                @if ($showDateRangePicker)
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('doctor.calendar.start_date') }}
                                </label>
                                <input type="date" wire:model.live="customStartDate"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('doctor.calendar.end_date') }}
                                </label>
                                <input type="date" wire:model.live="customEndDate"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                                <x-filament::button wire:click="setCustomDateRange" size="sm" color="primary">
                                    {{ __('doctor.calendar.apply') }}
                                </x-filament::button>
                                <x-filament::button wire:click="toggleDateRangePicker" size="sm" color="gray">
                                    {{ __('doctor.calendar.cancel') }}
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Active Filters Display --}}
                @if ($dateRangeStart && $dateRangeEnd)
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400">{{ __('doctor.calendar.active_filters') }}:</span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            <x-heroicon-m-calendar-days
                                class="w-3 h-3 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                            {{ \Carbon\Carbon::parse($dateRangeStart)->format('M j') }} -
                            {{ \Carbon\Carbon::parse($dateRangeEnd)->format('M j, Y') }}
                        </span>
                        @if ($appointmentTypeFilter !== 'all')
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                @if ($appointmentTypeFilter === 'online')
                                    <x-heroicon-m-video-camera
                                        class="w-3 h-3 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                                @else
                                    <x-heroicon-m-building-office-2
                                        class="w-3 h-3 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                                @endif
                                {{ __('doctor.appointment_types.' . $appointmentTypeFilter) }}
                            </span>
                        @endif
                        <x-filament::button wire:click="clearDateRange" size="sm" color="gray" class="text-xs">
                            {{ __('doctor.calendar.clear_filters') }}
                        </x-filament::button>
                    </div>
                @endif
            </div>

            {{-- Monthly Statistics --}}
            @php
                $stats = $this->getAppointmentStats();
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div
                    class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/30 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_slots'] }}</div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">{{ __('doctor.stats.total_bookings') }}</div>
                </div>
                <div
                    class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/30 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['available_slots'] }}
                    </div>
                    <div class="text-sm text-green-700 dark:text-green-300">{{ __('doctor.calendar.available') }}</div>
                </div>
                <div
                    class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/30 rounded-lg p-4">
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['booked_slots'] }}
                    </div>
                    <div class="text-sm text-orange-700 dark:text-orange-300">{{ __('doctor.calendar.booked') }}</div>
                </div>
                <div
                    class="bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/30 rounded-lg p-4">
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $stats['online_appointments'] }}</div>
                    <div class="text-sm text-emerald-700 dark:text-emerald-300">
                        {{ __('doctor.appointment_types.online') }}</div>
                </div>
                <div
                    class="bg-gradient-to-r from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/30 rounded-lg p-4">
                    <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">
                        {{ $stats['clinic_appointments'] }}</div>
                    <div class="text-sm text-cyan-700 dark:text-cyan-300">{{ __('doctor.appointment_types.clinic') }}
                    </div>
                </div>
            </div>

            {{-- Enhanced Calendar Grid with Mobile Support --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                @if ($showMobileView)
                    {{-- Mobile List View --}}
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $calendarData = $this->getCalendarData();
                            $days = $this->getCalendarDays();
                            $currentMonthDays = collect($days)->filter(fn($day) => $day['isCurrentMonth']);
                        @endphp

                        @foreach ($currentMonthDays as $day)
                            @php
                                $dateString = $day['date']->format('Y-m-d');
                                $dayData = $calendarData[$dateString] ?? null;
                            @endphp

                            <div class="p-4 {{ $day['isToday'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div
                                        class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                                        <span
                                            class="text-lg font-semibold text-gray-900 dark:text-white
                                            {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                            {{ $day['date']->format('j') }}
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $day['date']->format('l') }}
                                        </span>
                                    </div>

                                    @if (!$day['isPast'] && $day['isCurrentMonth'])
                                        <x-filament::button wire:click="selectDate('{{ $dateString }}')"
                                            size="sm" color="primary">
                                            <x-heroicon-m-plus class="w-4 h-4" />
                                        </x-filament::button>
                                    @endif
                                </div>

                                @if ($dayData)
                                    <div class="space-y-2">
                                        <div
                                            class="flex items-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $dayData['total'] }} {{ __('doctor.calendar.appointments') }}
                                            </span>

                                            @if ($dayData['online'] > 0)
                                                <div
                                                    class="flex items-center space-x-1 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                                                    <x-heroicon-m-video-camera class="w-4 h-4 text-emerald-600" />
                                                    <span
                                                        class="text-sm text-emerald-700 dark:text-emerald-300">{{ $dayData['online'] }}</span>
                                                </div>
                                            @endif

                                            @if ($dayData['clinic'] > 0)
                                                <div
                                                    class="flex items-center space-x-1 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                                                    <x-heroicon-m-building-office-2 class="w-4 h-4 text-cyan-600" />
                                                    <span
                                                        class="text-sm text-cyan-700 dark:text-cyan-300">{{ $dayData['clinic'] }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div
                                            class="flex items-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} text-xs">
                                            @if ($dayData['booked'] > 0)
                                                <span class="text-orange-600 dark:text-orange-400">
                                                    {{ $dayData['booked'] }} {{ __('doctor.calendar.booked') }}
                                                </span>
                                            @endif
                                            @if ($dayData['available'] > 0)
                                                <span class="text-green-600 dark:text-green-400">
                                                    {{ $dayData['available'] }} {{ __('doctor.calendar.available') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('doctor.calendar.no_appointments') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Desktop Grid View --}}
                    {{-- Day Headers --}}
                    <div
                        class="hidden md:grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @foreach (['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $day)
                            <div
                                class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                {{ __("doctor.calendar.days.{$day}") }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Mobile Day Headers (abbreviated) --}}
                    <div
                        class="grid md:hidden grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @foreach (['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $day)
                            <div
                                class="p-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                {{ __("doctor.calendar.days_short.{$day}") }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar Days --}}
                    <div class="grid grid-cols-7">
                        @php
                            $calendarData = $this->getCalendarData();
                            $days = $this->getCalendarDays();
                        @endphp

                        @foreach ($days as $day)
                            @php
                                $dateString = $day['date']->format('Y-m-d');
                                $dayData = $calendarData[$dateString] ?? null;
                                $densityLevel = $this->getDensityLevel($dayData);
                                $densityColor = $this->getDensityColor($densityLevel);
                            @endphp

                            <div class="relative min-h-[80px] md:min-h-[120px] border-r border-b border-gray-200 dark:border-gray-700 p-2 md:p-3 transition-all duration-200
                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-900 opacity-50' : $densityColor }}
                                {{ $day['isToday'] ? 'ring-2 ring-blue-500 ring-inset' : '' }}
                                {{ !$day['isPast'] && $day['isCurrentMonth'] ? 'hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer hover:shadow-md' : '' }}"
                                @if (!$day['isPast'] && $day['isCurrentMonth']) wire:click="selectDate('{{ $dateString }}')"
                                    title="{{ __('doctor.calendar.create_appointment_for') }} {{ $day['date']->format('M j, Y') }}" @endif>

                                {{-- Date Number --}}
                                <div class="flex items-center justify-between mb-1 md:mb-2">
                                    <span
                                        class="text-sm font-semibold
                                        {{ !$day['isCurrentMonth'] ? 'text-gray-400 dark:text-gray-600' : 'text-gray-900 dark:text-white' }}
                                        {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                        {{ $day['date']->day }}
                                    </span>

                                    @if (!$day['isPast'] && $day['isCurrentMonth'])
                                        <x-heroicon-m-plus
                                            class="w-3 h-3 md:w-4 md:h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                    @endif
                                </div>

                                {{-- Appointment Indicators --}}
                                @if ($dayData)
                                    <div class="space-y-1 md:space-y-2">
                                        {{-- Appointment Count Badge --}}
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 md:px-2 md:py-1 rounded-full text-xs font-medium
                                                {{ $densityLevel === 'high'
                                                    ? 'bg-blue-600 text-white'
                                                    : ($densityLevel === 'medium'
                                                        ? 'bg-blue-500 text-white'
                                                        : ($densityLevel === 'low'
                                                            ? 'bg-blue-400 text-white'
                                                            : 'bg-gray-200 text-gray-700')) }}">
                                                <span class="hidden md:inline">{{ $dayData['total'] }}
                                                    {{ __('doctor.calendar.appointments') }}</span>
                                                <span class="md:hidden">{{ $dayData['total'] }}</span>
                                            </span>
                                        </div>

                                        {{-- Appointment Type Indicators --}}
                                        <div class="flex flex-wrap gap-1">
                                            @if ($dayData['online'] > 0)
                                                <div
                                                    class="flex items-center space-x-1 bg-emerald-100 dark:bg-emerald-900/30 px-1.5 py-0.5 md:px-2 md:py-1 rounded-md">
                                                    <x-heroicon-m-video-camera
                                                        class="w-2.5 h-2.5 md:w-3 md:h-3 text-emerald-600 dark:text-emerald-400" />
                                                    <span
                                                        class="text-xs font-medium text-emerald-700 dark:text-emerald-300">{{ $dayData['online'] }}</span>
                                                </div>
                                            @endif

                                            @if ($dayData['clinic'] > 0)
                                                <div
                                                    class="flex items-center space-x-1 bg-cyan-100 dark:bg-cyan-900/30 px-1.5 py-0.5 md:px-2 md:py-1 rounded-md">
                                                    <x-heroicon-m-building-office-2
                                                        class="w-2.5 h-2.5 md:w-3 md:h-3 text-cyan-600 dark:text-cyan-400" />
                                                    <span
                                                        class="text-xs font-medium text-cyan-700 dark:text-cyan-300">{{ $dayData['clinic'] }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Status Indicators (hidden on mobile for space) --}}
                                        <div class="hidden md:flex justify-between text-xs">
                                            @if ($dayData['booked'] > 0)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                                    {{ $dayData['booked'] }} {{ __('doctor.calendar.booked') }}
                                                </span>
                                            @endif

                                            @if ($dayData['available'] > 0)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    {{ $dayData['available'] }} {{ __('doctor.calendar.available') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Empty day with subtle add indicator --}}
                                    @if (!$day['isPast'] && $day['isCurrentMonth'])
                                        <div
                                            class="hidden md:flex items-center justify-center h-16 opacity-0 hover:opacity-100 transition-opacity">
                                            <div class="flex flex-col items-center text-gray-400">
                                                <x-heroicon-m-plus class="w-6 h-6 mb-1" />
                                                <span
                                                    class="text-xs">{{ __('doctor.calendar.create_appointment') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Enhanced Legend --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    {{ __('doctor.calendar.legend') }}
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div
                        class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <x-heroicon-m-video-camera class="w-4 h-4 text-emerald-600" />
                        <span
                            class="text-gray-700 dark:text-gray-300">{{ __('doctor.appointment_types.online') }}</span>
                    </div>
                    <div
                        class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <x-heroicon-m-building-office-2 class="w-4 h-4 text-cyan-600" />
                        <span
                            class="text-gray-700 dark:text-gray-300">{{ __('doctor.appointment_types.clinic') }}</span>
                    </div>
                    <div
                        class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <span class="text-gray-700 dark:text-gray-300">{{ __('doctor.calendar.booked') }}</span>
                    </div>
                    <div
                        class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                        <span class="text-gray-700 dark:text-gray-300">{{ __('doctor.calendar.available') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Enhanced Quick Slot Creation Modal --}}
    <x-filament::modal id="create-appointment-modal" width="lg">
        <x-slot name="heading">
            <div class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                <x-heroicon-m-calendar-days class="w-6 h-6 text-blue-600" />
                <span>{{ __('doctor.calendar.create_appointment_for') }}
                    @if ($selectedDate)
                        {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                    @endif
                </span>
            </div>
        </x-slot>

        <div class="space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="flex items-start space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                    <x-heroicon-m-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            {{ __('doctor.calendar.quick_creation_info') }}</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            {{ __('doctor.calendar.create_appointment_description') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Action Buttons --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div
                        class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} mb-3">
                        <x-heroicon-m-video-camera class="w-6 h-6 text-emerald-600" />
                        <h5 class="font-medium text-gray-900 dark:text-white">
                            {{ __('doctor.appointment_types.online') }}</h5>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('doctor.calendar.online_description') }}</p>
                    <x-filament::button color="success" size="sm" class="w-full"
                        wire:click="createOnlineAppointment">
                        {{ __('doctor.calendar.create_online') }}
                    </x-filament::button>
                </div>

                <div
                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div
                        class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} mb-3">
                        <x-heroicon-m-building-office-2 class="w-6 h-6 text-cyan-600" />
                        <h5 class="font-medium text-gray-900 dark:text-white">
                            {{ __('doctor.appointment_types.clinic') }}</h5>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('doctor.calendar.clinic_description') }}</p>
                    <x-filament::button color="info" size="sm" class="w-full"
                        wire:click="createClinicAppointment">
                        {{ __('doctor.calendar.create_clinic') }}
                    </x-filament::button>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'create-appointment-modal' })">
                    {{ __('doctor.calendar.cancel') }}
                </x-filament::button>

                <x-filament::button color="primary" wire:click="createAppointmentSlot">
                    <x-heroicon-m-cog-6-tooth class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" />
                    {{ __('doctor.calendar.advanced_creation') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</x-filament-widgets::widget>
