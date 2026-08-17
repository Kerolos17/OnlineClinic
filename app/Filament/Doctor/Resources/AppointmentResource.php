<?php

namespace App\Filament\Doctor\Resources;

use App\Filament\Doctor\Resources\AppointmentResource\Pages;
use App\Models\Slot;
use App\Services\DoctorSlotService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AppointmentResource extends Resource
{
    protected static ?string $model = Slot::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'appointments';

    public static function getNavigationLabel(): string
    {
        return __('doctor.appointments');
    }

    public static function getModelLabel(): string
    {
        return __('doctor.appointment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('doctor.appointments');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('doctor.booking_warning'))
                    ->schema([
                        Forms\Components\Placeholder::make('booking_warning')
                            ->content(function ($record) {
                                if ($record && $record->booking) {
                                    return __('doctor.editing_booked_appointment_warning_detailed', [
                                        'patient' => $record->booking->patient_name,
                                        'phone' => $record->booking->patient_phone,
                                    ]);
                                }

                                return '';
                            })
                            ->visible(fn ($record) => $record && $record->booking)
                            ->extraAttributes(['class' => 'text-warning-600 bg-warning-50 p-4 rounded-lg border border-warning-200']),
                    ])
                    ->visible(fn ($record) => $record && $record->booking)
                    ->collapsible(false),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('doctor.appointment_type'))
                            ->options([
                                'online' => __('doctor.online_consultation'),
                                'clinic' => __('doctor.clinic_visit'),
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $record) {
                                if ($state === 'clinic') {
                                    $set('zoom_meeting_id', null);
                                    $set('zoom_join_url', null);
                                    $set('zoom_start_url', null);
                                }
                                if ($record && $record->booking && $record->type !== $state) {
                                    $set('type_change_warning', true);
                                }
                            })
                            ->helperText(function ($record) {
                                if ($record && $record->booking && $record->type === 'online') {
                                    return __('doctor.appointment_type_change_zoom_warning');
                                }

                                return __('doctor.appointment_type_help');
                            }),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('doctor.date'))
                            ->required()
                            ->minDate(now())
                            ->maxDate(now()->addMonths(3))
                            ->displayFormat('Y-m-d')
                            ->helperText(__('doctor.date_help')),

                        Forms\Components\TimePicker::make('start_time')
                            ->label(__('doctor.start_time'))
                            ->required()
                            ->seconds(false)
                            ->reactive()
                            ->helperText(__('doctor.start_time_help')),

                        Forms\Components\TimePicker::make('end_time')
                            ->label(__('doctor.end_time'))
                            ->required()
                            ->seconds(false)
                            ->after('start_time')
                            ->reactive()
                            ->helperText(__('doctor.end_time_help')),
                    ]),

                Forms\Components\Section::make(__('doctor.appointment_details'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('doctor.notes'))
                            ->visible(fn (Forms\Get $get) => $get('type') === 'clinic')
                            ->placeholder(__('doctor.clinic_instructions_placeholder'))
                            ->helperText(__('doctor.clinic_notes_help'))
                            ->rows(3),

                        Forms\Components\Placeholder::make('zoom_info')
                            ->label(__('doctor.zoom_meeting_info'))
                            ->visible(fn (Forms\Get $get) => $get('type') === 'online')
                            ->content(function ($record, Forms\Get $get) {
                                if ($record && $record->hasZoomMeeting() && $get('type') === 'online') {
                                    return __('doctor.zoom_meeting_will_be_updated');
                                }

                                return __('doctor.zoom_auto_creation_info');
                            }),

                        Forms\Components\Hidden::make('type_change_warning')
                            ->default(false),
                    ]),

                Forms\Components\Section::make(__('doctor.bulk_creation'))
                    ->schema([
                        Forms\Components\Toggle::make('create_bulk')
                            ->label(__('doctor.create_recurring_slots'))
                            ->reactive()
                            ->helperText(__('doctor.bulk_creation_help')),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('end_date')
                                    ->label(__('doctor.end_date'))
                                    ->visible(fn (Forms\Get $get) => $get('create_bulk'))
                                    ->minDate(fn (Forms\Get $get) => $get('date'))
                                    ->maxDate(now()->addMonths(3))
                                    ->helperText(__('doctor.end_date_help')),

                                Forms\Components\CheckboxList::make('recurring_days')
                                    ->label(__('doctor.recurring_days'))
                                    ->visible(fn (Forms\Get $get) => $get('create_bulk'))
                                    ->options([
                                        'monday' => __('doctor.monday'),
                                        'tuesday' => __('doctor.tuesday'),
                                        'wednesday' => __('doctor.wednesday'),
                                        'thursday' => __('doctor.thursday'),
                                        'friday' => __('doctor.friday'),
                                        'saturday' => __('doctor.saturday'),
                                        'sunday' => __('doctor.sunday'),
                                    ])
                                    ->columns(2)
                                    ->helperText(__('doctor.recurring_days_help')),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => ! $record),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('doctor.date'))
                    ->date('M j, Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('doctor.time'))
                    ->formatStateUsing(fn ($record) => $record->start_time.' - '.$record->end_time)
                    ->sortable(),

                Tables\Columns\IconColumn::make('type')
                    ->label(__('doctor.type'))
                    ->icon(fn (string $state): string => match ($state) {
                        'online' => 'heroicon-o-video-camera',
                        'clinic' => 'heroicon-o-building-office-2',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'info',
                        'clinic' => 'success',
                        default => 'gray',
                    })
                    ->tooltip(fn ($record) => $record->type === 'online'
                            ? __('doctor.online_consultation')
                            : __('doctor.clinic_visit')),

                // ✅ FIX: BadgeColumn → TextColumn->badge()
                Tables\Columns\TextColumn::make('status')
                    ->label(__('doctor.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'booked' => 'warning',
                        'cancelled' => 'danger',
                        'blocked' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("doctor.status_{$state}")),

                Tables\Columns\TextColumn::make('booking.patient_name')
                    ->label(__('doctor.patient'))
                    ->placeholder(__('doctor.no_booking'))
                    ->searchable(),

                // ✅ FIX: zoom_label بدل zoom + شيل visible لأنه مش بيشتغل على table level
                Tables\Columns\IconColumn::make('zoom_meeting_id')
                    ->label(__('doctor.zoom_label'))
                    ->boolean()
                    ->toggleable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->hasZoomMeeting()
                            ? __('doctor.zoom_meeting_ready')
                            : __('doctor.zoom_meeting_missing')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('doctor.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('doctor.appointment_type'))
                    ->options([
                        'online' => __('doctor.online_consultation'),
                        'clinic' => __('doctor.clinic_visit'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('doctor.status'))
                    ->options([
                        'available' => __('doctor.status_available'),
                        'booked' => __('doctor.status_booked'),
                        'cancelled' => __('doctor.status_cancelled'),
                        'blocked' => __('doctor.status_blocked'),
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('doctor.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('doctor.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = __('doctor.from_date').': '.$data['from'];
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('doctor.until_date').': '.$data['until'];
                        }

                        return $indicators;
                    }),

                Tables\Filters\Filter::make('future_only')
                    ->label(__('doctor.future_appointments_only'))
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', '>=', now()->toDateString()))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Slot $record) => $record->status === 'available'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Slot $record) => $record->status === 'available')
                    ->requiresConfirmation()
                    ->modalHeading(__('doctor.delete_appointment'))
                    ->modalDescription(function (Slot $record) {
                        if ($record->booking) {
                            if ($record->date->isToday() || $record->date->isTomorrow()) {
                                return __('doctor.delete_booked_appointment_24h_warning');
                            }

                            return __('doctor.delete_booked_appointment_warning_detailed', [
                                'patient' => $record->booking->patient_name,
                                'phone' => $record->booking->patient_phone,
                            ]);
                        }

                        return __('doctor.delete_appointment_confirmation');
                    })
                    ->modalSubmitActionLabel(__('doctor.delete'))
                    ->before(function (Slot $record) {
                        if ($record->booking &&
                            ($record->date->isToday() || $record->date->isTomorrow())) {
                            throw ValidationException::withMessages([
                                'appointment' => __('doctor.cannot_delete_appointment_24h_protection'),
                            ]);
                        }
                    })
                    ->action(function (Slot $record) {
                        $doctorSlotService = app(DoctorSlotService::class);
                        try {
                            $doctorSlotService->deleteSlot($record);
                        } catch (\Exception $e) {
                            throw ValidationException::withMessages([
                                'appointment' => __('doctor.appointment_deletion_failed', ['error' => $e->getMessage()]),
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('doctor.delete_appointments'))
                        ->modalDescription(__('doctor.delete_appointments_confirmation_detailed'))
                        ->modalSubmitActionLabel(__('doctor.delete'))
                        ->before(function ($records) {
                            $protectedAppointments = $records->filter(function ($record) {
                                return $record->booking &&
                                    ($record->date->isToday() || $record->date->isTomorrow());
                            });
                            if ($protectedAppointments->isNotEmpty()) {
                                throw ValidationException::withMessages([
                                    'appointments' => __('doctor.cannot_delete_appointments_24h_protection', [
                                        'count' => $protectedAppointments->count(),
                                    ]),
                                ]);
                            }
                        })
                        ->action(function ($records) {
                            $doctorSlotService = app(DoctorSlotService::class);
                            $deletedCount = 0;
                            $errors = [];
                            foreach ($records as $record) {
                                try {
                                    $doctorSlotService->deleteSlot($record);
                                    $deletedCount++;
                                } catch (\Exception $e) {
                                    $errors[] = $e->getMessage();
                                }
                            }
                            if (! empty($errors)) {
                                throw ValidationException::withMessages([
                                    'appointments' => __('doctor.bulk_deletion_partial_failure', [
                                        'deleted' => $deletedCount,
                                        'total' => $records->count(),
                                        'errors' => implode(', ', array_unique($errors)),
                                    ]),
                                ]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->defaultSort('start_time', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $doctorId = $user?->doctor?->id;

        if (! $doctorId) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return Slot::query()
            ->forDoctor($doctorId)
            ->with(['booking', 'doctor.user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->doctor !== null;
    }

    public static function canEdit(Model $record): bool
    {
        $record = $record instanceof Slot ? $record : Slot::find($record->getKey());

        return $record && $record->doctor_id === Auth::user()?->doctor?->id
        && $record->status === 'available';
    }

    public static function canDelete(Model $record): bool
    {
        $record = $record instanceof Slot ? $record : Slot::find($record->getKey());

        return $record && $record->doctor_id === Auth::user()?->doctor?->id
        && $record->status === 'available';
    }

    public static function canView(Model $record): bool
    {
        $record = $record instanceof Slot ? $record : Slot::find($record->getKey());

        return $record && $record->doctor_id === Auth::user()?->doctor?->id;
    }
}
