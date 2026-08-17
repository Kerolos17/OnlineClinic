<?php

namespace App\Filament\Doctor\Resources\AppointmentResource\Pages;

use App\Filament\Doctor\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read \App\Models\Slot $record
 */
class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return __('doctor.view_appointment');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->status === 'available'),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status === 'available')
                ->requiresConfirmation()
                ->modalHeading(__('doctor.delete_appointment'))
                ->modalDescription(function () {
                    if ($this->record->booking) {
                        return __('doctor.delete_booked_appointment_warning');
                    }

                    return __('doctor.delete_appointment_confirmation');
                })
                ->modalSubmitActionLabel(__('doctor.delete')),
        ];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        // Ensure the appointment belongs to the current doctor
        if ($this->record->doctor_id !== Auth::user()->doctor->id) {
            abort(403);
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('doctor.appointment_details'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('date')
                                    ->label(__('doctor.date'))
                                    ->date('l, F j, Y'),

                                Infolists\Components\TextEntry::make('time_range')
                                    ->label(__('doctor.time'))
                                    ->formatStateUsing(fn ($record) => $record->start_time.' - '.$record->end_time),

                                Infolists\Components\TextEntry::make('type')
                                    ->label(__('doctor.appointment_type'))
                                    ->formatStateUsing(fn (string $state): string => $state === 'online'
                                            ? __('doctor.online_consultation')
                                            : __('doctor.clinic_visit')
                                    )
                                    ->icon(fn (string $state): string => match ($state) {
                                        'online' => 'heroicon-o-video-camera',
                                        'clinic' => 'heroicon-o-building-office-2',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'online' => 'info',
                                        'clinic' => 'success',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('doctor.status'))
                                    ->formatStateUsing(fn (string $state): string => __("doctor.status_{$state}"))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'available' => 'success',
                                        'booked' => 'warning',
                                        'cancelled' => 'danger',
                                        'blocked' => 'gray',
                                        default => 'gray',
                                    }),
                            ]),

                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('doctor.notes'))
                            ->visible(fn ($record) => ! empty($record->notes))
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make(__('doctor.zoom_meeting_details'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('zoom_meeting_id')
                                    ->label(__('doctor.zoom_meeting_id'))
                                    ->visible(fn ($record) => ! empty($record->zoom_meeting_id))
                                    ->copyable()
                                    ->copyMessage(__('doctor.zoom_link_copied')),

                                Infolists\Components\TextEntry::make('zoom_status')
                                    ->label(__('doctor.zoom_meeting_status'))
                                    ->formatStateUsing(function ($record) {
                                        if (! $record->hasZoomMeeting()) {
                                            return __('doctor.zoom_meeting_missing');
                                        }

                                        // Check meeting status via ZoomService
                                        try {
                                            $zoomService = app(\App\Services\ZoomService::class);
                                            $status = $zoomService->getMeetingStatus($record->zoom_meeting_id);

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
                                    ->badge()
                                    ->color(function ($record) {
                                        if (! $record->hasZoomMeeting()) {
                                            return 'danger';
                                        }

                                        try {
                                            $zoomService = app(\App\Services\ZoomService::class);
                                            $status = $zoomService->getMeetingStatus($record->zoom_meeting_id);

                                            return match ($status['status']) {
                                                'waiting' => 'warning',
                                                'started' => 'success',
                                                'ended' => 'gray',
                                                default => 'info'
                                            };
                                        } catch (\Exception $e) {
                                            return 'danger';
                                        }
                                    }),
                            ]),

                        Infolists\Components\Section::make(__('doctor.zoom_meeting_controls'))
                            ->schema([
                                Infolists\Components\Actions::make([
                                    Infolists\Components\Actions\Action::make('start_zoom')
                                        ->label(__('doctor.start_zoom_meeting'))
                                        ->icon('heroicon-o-video-camera')
                                        ->color('primary')
                                        ->url(fn ($record) => $record->zoom_start_url)
                                        ->openUrlInNewTab()
                                        ->visible(fn ($record) => ! empty($record->zoom_start_url)),

                                    Infolists\Components\Actions\Action::make('copy_join_url')
                                        ->label(__('doctor.copy_patient_link'))
                                        ->icon('heroicon-o-clipboard')
                                        ->color('gray')
                                        ->action(function ($record) {
                                            $this->js('navigator.clipboard.writeText("'.$record->zoom_join_url.'")');
                                            \Filament\Notifications\Notification::make()
                                                ->title(__('doctor.zoom_link_copied'))
                                                ->success()
                                                ->send();
                                        })
                                        ->visible(fn ($record) => ! empty($record->zoom_join_url)),

                                    Infolists\Components\Actions\Action::make('recreate_meeting')
                                        ->label(__('doctor.recreate_zoom_meeting'))
                                        ->icon('heroicon-o-arrow-path')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->modalHeading(__('doctor.recreate_zoom_meeting'))
                                        ->modalDescription(__('doctor.manual_zoom_creation_help'))
                                        ->action(function ($record) {
                                            try {
                                                $zoomService = app(\App\Services\ZoomService::class);
                                                $doctorSlotService = app(\App\Services\DoctorSlotService::class);

                                                // Delete existing meeting if it exists
                                                if ($record->zoom_meeting_id) {
                                                    try {
                                                        $zoomService->deleteMeeting($record->zoom_meeting_id);
                                                    } catch (\Exception $e) {
                                                        // Continue even if deletion fails
                                                    }
                                                }

                                                // Create new meeting
                                                $topic = "Consultation with Dr. {$record->doctor->user->name}";
                                                $startTime = $record->date->format('Y-m-d').'T'.$record->start_time.':00';
                                                $duration = (int) \Carbon\Carbon::parse($record->end_time)->diffInMinutes(\Carbon\Carbon::parse($record->start_time));

                                                $meeting = $zoomService->createMeetingForAppointment(
                                                    $record->doctor->user->email,
                                                    $topic,
                                                    $startTime,
                                                    $duration,
                                                    $record
                                                );

                                                // Update slot with new meeting details
                                                $record->update([
                                                    'zoom_meeting_id' => $meeting['id'],
                                                    'zoom_join_url' => $meeting['join_url'],
                                                    'zoom_start_url' => $meeting['start_url'],
                                                ]);

                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('doctor.zoom_meeting_created'))
                                                    ->success()
                                                    ->send();
                                                $this->redirect(request()->header('Referer'));

                                            } catch (\Exception $e) {
                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('doctor.zoom_meeting_creation_failed', ['error' => $e->getMessage()]))
                                                    ->danger()
                                                    ->send();
                                            }
                                        })
                                        ->visible(fn ($record) => $record->type === 'online'),

                                    Infolists\Components\Actions\Action::make('update_meeting')
                                        ->label(__('doctor.update_zoom_meeting'))
                                        ->icon('heroicon-o-cog-6-tooth')
                                        ->color('info')
                                        ->form([
                                            \Filament\Forms\Components\Toggle::make('waiting_room')
                                                ->label(__('doctor.zoom_settings.waiting_room'))
                                                ->helperText(__('doctor.zoom_settings.waiting_room_help'))
                                                ->default(true),

                                            \Filament\Forms\Components\Select::make('recording')
                                                ->label(__('doctor.zoom_settings.recording'))
                                                ->helperText(__('doctor.zoom_settings.recording_help'))
                                                ->options([
                                                    'none' => __('doctor.zoom_settings.recording_none'),
                                                    'local' => __('doctor.zoom_settings.recording_local'),
                                                    'cloud' => __('doctor.zoom_settings.recording_cloud'),
                                                ])
                                                ->default('none'),

                                            \Filament\Forms\Components\Toggle::make('mute_upon_entry')
                                                ->label(__('doctor.zoom_settings.mute_participants'))
                                                ->helperText(__('doctor.zoom_settings.mute_participants_help'))
                                                ->default(false),
                                        ])
                                        ->action(function ($record, array $data) {
                                            try {
                                                $zoomService = app(\App\Services\ZoomService::class);

                                                $settings = [
                                                    'waiting_room' => $data['waiting_room'],
                                                    'auto_recording' => $data['recording'],
                                                    'mute_upon_entry' => $data['mute_upon_entry'],
                                                ];

                                                $zoomService->updateMeetingSettings($record->zoom_meeting_id, $settings);

                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('doctor.zoom_meeting_updated'))
                                                    ->success()
                                                    ->send();

                                            } catch (\Exception $e) {
                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('doctor.zoom_meeting_update_failed', ['error' => $e->getMessage()]))
                                                    ->danger()
                                                    ->send();
                                            }
                                        })
                                        ->visible(fn ($record) => ! empty($record->zoom_meeting_id)),
                                ]),
                            ])
                            ->visible(fn ($record) => $record->type === 'online'),

                        // Fallback options when Zoom meeting is missing
                        Infolists\Components\Section::make(__('doctor.zoom_fallback_options'))
                            ->schema([
                                Infolists\Components\TextEntry::make('zoom_api_unavailable')
                                    ->label(__('doctor.zoom_api_unavailable'))
                                    ->state(__('doctor.zoom_api_unavailable'))
                                    ->color('warning')
                                    ->extraAttributes(['class' => 'text-warning-600']),

                                Infolists\Components\Actions::make([
                                    Infolists\Components\Actions\Action::make('create_manual_meeting')
                                        ->label(__('doctor.create_manual_meeting'))
                                        ->icon('heroicon-o-plus')
                                        ->color('success')
                                        ->url('https://zoom.us/start/webmeeting')
                                        ->openUrlInNewTab(),
                                ]),
                            ])
                            ->visible(fn ($record) => $record->type === 'online' && ! $record->hasZoomMeeting()),
                    ])
                    ->visible(fn ($record) => $record->type === 'online'),

                Infolists\Components\Section::make(__('doctor.booking_information'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('booking.patient_name')
                                    ->label(__('doctor.patient_name')),

                                Infolists\Components\TextEntry::make('booking.patient_email')
                                    ->label(__('doctor.patient_email')),

                                Infolists\Components\TextEntry::make('booking.patient_phone')
                                    ->label(__('doctor.patient_phone')),

                                Infolists\Components\TextEntry::make('booking.status')
                                    ->label(__('doctor.booking_status'))
                                    ->formatStateUsing(fn (?string $state): string => $state ? __("doctor.booking_status_{$state}") : __('doctor.no_booking')
                                    )
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),

                        Infolists\Components\TextEntry::make('booking.notes')
                            ->label(__('doctor.patient_notes'))
                            ->visible(fn ($record) => ! empty($record->booking->notes))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->booking !== null),

                Infolists\Components\Section::make(__('doctor.system_information'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('doctor.created_at'))
                                    ->dateTime('F j, Y g:i A'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('doctor.updated_at'))
                                    ->dateTime('F j, Y g:i A'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
