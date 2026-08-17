<?php

namespace App\Filament\Doctor\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UpcomingAppointmentsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = null;

    public function getTableHeading(): ?string
    {
        return __('doctor.widgets.upcoming_appointments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('appointment_at')
                    ->label(__('doctor.fields.appointment_time'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient_name')
                    ->label(__('doctor.fields.patient_name'))
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('patient_phone')
                    ->label(__('doctor.fields.patient_phone'))
                    ->copyable()
                    ->copyMessage(__('doctor.messages.phone_copied')),

                Tables\Columns\IconColumn::make('slot.type')
                    ->label(__('doctor.fields.appointment_type'))
                    ->icon(fn (string $state): string => match ($state) {
                        'online' => 'heroicon-o-video-camera',
                        'clinic' => 'heroicon-o-building-office-2',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'clinic' => 'primary',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        'online' => __('doctor.appointment_types.online'),
                        'clinic' => __('doctor.appointment_types.clinic'),
                        default => __('doctor.appointment_types.unknown'),
                    }),

                Tables\Columns\BadgeColumn::make('zoom_status')
                    ->label(__('doctor.zoom_meeting_status'))
                    ->formatStateUsing(function (Booking $record): string {
                        if ($record->slot?->type !== 'online') {
                            return '';
                        }

                        if (! $record->slot->hasZoomMeeting()) {
                            return __('doctor.zoom_meeting_missing');
                        }

                        try {
                            $zoomService = app(\App\Services\ZoomService::class);
                            $status = $zoomService->getMeetingStatus($record->slot->zoom_meeting_id);

                            return match ($status['status']) {
                                'waiting' => __('doctor.zoom_meeting_scheduled'),
                                'started' => __('doctor.zoom_meeting_active'),
                                'ended' => __('doctor.zoom_meeting_ended'),
                                default => __('doctor.zoom_meeting_ready')
                            };
                        } catch (\Exception $e) {
                            return __('doctor.zoom_meeting_error');
                        }
                    })
                    ->color(function (Booking $record): string {
                        if ($record->slot?->type !== 'online') {
                            return 'gray';
                        }

                        if (! $record->slot->hasZoomMeeting()) {
                            return 'danger';
                        }

                        try {
                            $zoomService = app(\App\Services\ZoomService::class);
                            $status = $zoomService->getMeetingStatus($record->slot->zoom_meeting_id);

                            return match ($status['status']) {
                                'waiting' => 'warning',
                                'started' => 'success',
                                'ended' => 'gray',
                                default => 'info'
                            };
                        } catch (\Exception $e) {
                            return 'danger';
                        }
                    })
                    ->visible(fn (Booking $record): bool => $record->slot?->type === 'online'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('doctor.fields.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => __("doctor.booking_status.{$state}")),

                Tables\Columns\TextColumn::make('patient_notes')
                    ->label(__('doctor.fields.patient_notes'))
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return $state;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('start_zoom')
                    ->label(__('doctor.actions.start_zoom'))
                    ->icon('heroicon-o-video-camera')
                    ->color('success')
                    ->url(fn (Booking $record): ?string => $record->slot?->zoom_start_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record): bool => $record->slot?->type === 'online' &&
                        ! empty($record->slot->zoom_start_url) &&
                        $record->appointment_at?->isToday()
                    ),

                Tables\Actions\Action::make('copy_patient_link')
                    ->label(__('doctor.copy_patient_link'))
                    ->icon('heroicon-o-clipboard')
                    ->color('gray')
                    ->action(function (Booking $record) {
                        $this->js('navigator.clipboard.writeText("'.$record->slot?->zoom_join_url.'")');
                        \Filament\Notifications\Notification::make()
                            ->title(__('doctor.zoom_link_copied'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Booking $record): bool => $record->slot?->type === 'online' &&
                        ! empty($record->slot->zoom_join_url)
                    ),

                Tables\Actions\Action::make('recreate_zoom')
                    ->label(__('doctor.recreate_zoom_meeting'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('doctor.recreate_zoom_meeting'))
                    ->modalDescription(__('doctor.manual_zoom_creation_help'))
                    ->action(function (Booking $record) {
                        try {
                            $zoomService = app(\App\Services\ZoomService::class);
                            $slot = $record->slot;

                            // Delete existing meeting if it exists
                            if ($slot->zoom_meeting_id) {
                                try {
                                    $zoomService->deleteMeeting($slot->zoom_meeting_id);
                                } catch (\Exception $e) {
                                    // Continue even if deletion fails
                                }
                            }

                            // Create new meeting
                            $topic = "Consultation with Dr. {$slot->doctor->user->name}";
                            $startTime = $slot->date->format('Y-m-d').'T'.$slot->start_time.':00';
                            $duration = (int) \Carbon\Carbon::parse($slot->end_time)->diffInMinutes(\Carbon\Carbon::parse($slot->start_time));

                            $meeting = $zoomService->createMeetingForAppointment(
                                $slot->doctor->user->email,
                                $topic,
                                $startTime,
                                $duration,
                                $slot
                            );

                            // Update slot with new meeting details
                            $slot->update([
                                'zoom_meeting_id' => $meeting['id'],
                                'zoom_join_url' => $meeting['join_url'],
                                'zoom_start_url' => $meeting['start_url'],
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title(__('doctor.zoom_meeting_created'))
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('doctor.zoom_meeting_creation_failed', ['error' => $e->getMessage()]))
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Booking $record): bool => $record->slot?->type === 'online' &&
empty($record->slot->zoom_meeting_id)
                    ),

                Tables\Actions\Action::make('view_zoom')
                    ->label(__('doctor.actions.view_zoom_details'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(fn (Booking $record) => view('filament.doctor.modals.zoom-details', [
                        'booking' => $record,
                    ]))
                    ->visible(fn (Booking $record): bool => $record->slot?->type === 'online' &&
                        ! empty($record->slot->zoom_meeting_id)
                    ),

                Tables\Actions\Action::make('add_notes')
                    ->label(__('doctor.actions.add_notes'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('doctor_notes')
                            ->label(__('doctor.fields.doctor_notes'))
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->fillForm(fn (Booking $record): array => [
                        'doctor_notes' => $record->doctor_notes,
                    ])
                    ->action(function (array $data, Booking $record): void {
                        $record->update([
                            'doctor_notes' => $data['doctor_notes'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('doctor.messages.notes_updated'))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('appointment_at', 'asc')
            ->paginated([5]);
    }

    protected function getTableQuery(): Builder
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return Booking::query()->where('id', 0); // Return empty query
        }

        return Booking::query()
            ->where('doctor_id', $doctor->id)
            ->where('appointment_at', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['slot']);
    }
}
