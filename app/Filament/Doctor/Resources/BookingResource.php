<?php

namespace App\Filament\Doctor\Resources;

use App\Filament\Doctor\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'bookings';

    public static function getNavigationLabel(): string
    {
        return __('doctor.bookings');
    }

    public static function getModelLabel(): string
    {
        return __('doctor.booking');
    }

    public static function getPluralModelLabel(): string
    {
        return __('doctor.bookings');
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $doctorId = $user?->doctor?->id;

        if (! $doctorId) {
            return null;
        }

        // Count bookings requiring attention (pending or upcoming within 24 hours)
        $count = Booking::where('doctor_id', $doctorId)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($q) {
                        $q->where('status', 'confirmed')
                            ->where('appointment_at', '>=', now())
                            ->where('appointment_at', '<=', now()->addDay());
                    });
            })
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('doctor.booking_information'))
                    ->schema([
                        Forms\Components\Placeholder::make('appointment_details')
                            ->label(__('doctor.appointment'))
                            ->content(function ($record) {
                                if (! $record || ! $record->slot) {
                                    return __('doctor.no_appointment_data');
                                }

                                $slot = $record->slot;
                                $type = $slot->isOnline()
                                    ? __('doctor.online_consultation')
                                    : __('doctor.clinic_visit');

                                return $slot->date->format('M j, Y').' | '.
                                $slot->start_time.' - '.$slot->end_time.' | '.$type;
                            }),

                        Forms\Components\Select::make('status')
                            ->label(__('doctor.booking_status'))
                            ->options([
                                'pending' => __('doctor.status_pending'),
                                'confirmed' => __('doctor.status_confirmed'),
                                'completed' => __('doctor.status_completed'),
                                'cancelled' => __('doctor.status_cancelled'),
                            ])
                            ->required()
                            ->helperText(__('doctor.booking_status_help')),
                    ]),

                Forms\Components\Section::make(__('doctor.patient_information'))
                    ->schema([
                        Forms\Components\TextInput::make('patient_name')
                            ->label(__('doctor.patient_name'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('patient_email')
                            ->label(__('doctor.patient_email'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('patient_phone')
                            ->label(__('doctor.patient_phone'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('patient_notes')
                            ->label(__('doctor.patient_notes'))
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3),
                    ]),

                Forms\Components\Section::make(__('doctor.doctor_notes'))
                    ->schema([
                        Forms\Components\Textarea::make('doctor_notes')
                            ->label(__('doctor.private_notes'))
                            ->placeholder(__('doctor.private_notes_placeholder'))
                            ->helperText(__('doctor.private_notes_help'))
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appointment_at')
                    ->label(__('doctor.appointment_time'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->description(fn ($record) => $record->slot?->isOnline()
                            ? __('doctor.online_consultation')
                            : __('doctor.clinic_visit')),

                Tables\Columns\TextColumn::make('patient_name')
                    ->label(__('doctor.patient'))
                    ->searchable()
                    ->description(fn ($record) => $record->patient_phone),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('doctor.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => __("doctor.status_{$state}")),

                Tables\Columns\IconColumn::make('slot.type')
                    ->label(__('doctor.type'))
                    ->icon(fn ($record) => $record->slot?->isOnline()
                            ? 'heroicon-o-video-camera'
                            : 'heroicon-o-building-office-2')
                    ->color(fn ($record) => $record->slot?->isOnline() ? 'info' : 'success')
                    ->tooltip(fn ($record) => $record->slot?->isOnline()
                            ? __('doctor.online_consultation')
                            : __('doctor.clinic_visit')),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('doctor.amount'))
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\IconColumn::make('requires_attention')
                    ->label(__('doctor.attention'))
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        // Requires attention if pending or upcoming within 24 hours
                        return $record->status === 'pending' ||
                            ($record->status === 'confirmed' &&
                            $record->appointment_at >= now() &&
                            $record->appointment_at <= now()->addDay());
                    })
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn ($record) => $record->status === 'pending'
                            ? __('doctor.pending_confirmation')
                            : ($record->appointment_at >= now() && $record->appointment_at <= now()->addDay()
                                ? __('doctor.upcoming_appointment')
                                : __('doctor.no_attention_needed'))),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('doctor.booked_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('doctor.status'))
                    ->options([
                        'pending' => __('doctor.status_pending'),
                        'confirmed' => __('doctor.status_confirmed'),
                        'completed' => __('doctor.status_completed'),
                        'cancelled' => __('doctor.status_cancelled'),
                    ]),

                Tables\Filters\SelectFilter::make('appointment_type')
                    ->label(__('doctor.appointment_type'))
                    ->options([
                        'online' => __('doctor.online_consultation'),
                        'clinic' => __('doctor.clinic_visit'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('slot', function ($q) use ($data) {
                            $q->where('type', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('requires_attention')
                    ->label(__('doctor.requires_attention'))
                    ->query(function (Builder $query): Builder {
                        return $query->where(function ($q) {
                            $q->where('status', 'pending')
                                ->orWhere(function ($subQ) {
                                    $subQ->where('status', 'confirmed')
                                        ->where('appointment_at', '>=', now())
                                        ->where('appointment_at', '<=', now()->addDay());
                                });
                        });
                    }),

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
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'confirmed'])),

                Tables\Actions\Action::make('zoom_meeting')
                    ->label(__('doctor.join_zoom'))
                    ->icon('heroicon-o-video-camera')
                    ->color('info')
                    ->url(fn (Booking $record) => $record->zoom_start_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record) => $record->slot?->isOnline() && ! empty($record->zoom_start_url)),

                Tables\Actions\Action::make('confirm_booking')
                    ->label(__('doctor.confirm'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'confirmed']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('doctor.confirm_booking'))
                    ->modalDescription(__('doctor.confirm_booking_description'))
                    ->visible(fn (Booking $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('complete_booking')
                    ->label(__('doctor.complete'))
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'completed']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('doctor.complete_booking'))
                    ->modalDescription(__('doctor.complete_booking_description'))
                    ->visible(fn (Booking $record) => $record->status === 'confirmed' && $record->appointment_at < now()),

                Tables\Actions\Action::make('send_reminder')
                    ->label(__('doctor.send_reminder'))
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->action(function (Booking $record) {
                        // TODO: Implement reminder sending logic
                        // This would typically send an email or SMS reminder
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('doctor.send_reminder'))
                    ->modalDescription(__('doctor.send_reminder_description'))
                    ->visible(fn (Booking $record) => $record->status === 'confirmed' && $record->appointment_at > now()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_selected')
                        ->label(__('doctor.confirm_selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'confirmed']);
                                }
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('doctor.confirm_selected_bookings'))
                        ->modalDescription(__('doctor.confirm_selected_description')),
                ]),
            ])
            ->defaultSort('appointment_at', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('doctor.appointment_details'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('appointment_at')
                                    ->label(__('doctor.appointment_time'))
                                    ->dateTime('M j, Y g:i A'),

                                Infolists\Components\TextEntry::make('slot.type')
                                    ->label(__('doctor.appointment_type'))
                                    ->formatStateUsing(fn ($record) => $record->slot?->isOnline()
                                            ? __('doctor.online_consultation')
                                            : __('doctor.clinic_visit'))
                                    ->badge()
                                    ->color(fn ($record) => $record->slot?->isOnline() ? 'info' : 'success'),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('doctor.status'))
                                    ->formatStateUsing(fn (string $state): string => __("doctor.status_{$state}"))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label(__('doctor.amount'))
                                    ->money('EGP'),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('doctor.patient_information'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('patient_name')
                                    ->label(__('doctor.patient_name')),

                                Infolists\Components\TextEntry::make('patient_email')
                                    ->label(__('doctor.patient_email'))
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('patient_phone')
                                    ->label(__('doctor.patient_phone'))
                                    ->copyable(),
                            ]),

                        Infolists\Components\TextEntry::make('patient_notes')
                            ->label(__('doctor.patient_notes'))
                            ->placeholder(__('doctor.no_patient_notes'))
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make(__('doctor.appointment_access'))
                    ->schema([
                        Infolists\Components\TextEntry::make('zoom_join_url')
                            ->label(__('doctor.patient_zoom_link'))
                            ->placeholder(__('doctor.no_zoom_link'))
                            ->formatStateUsing(fn ($state) => $state ? 'Join Meeting' : null)
                            ->url(fn ($record) => $record->zoom_join_url)
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => $record->slot?->isOnline()),

                        Infolists\Components\TextEntry::make('zoom_start_url')
                            ->label(__('doctor.doctor_zoom_link'))
                            ->placeholder(__('doctor.no_zoom_link'))
                            ->formatStateUsing(fn ($state) => $state ? 'Start Meeting' : null)
                            ->url(fn ($record) => $record->zoom_start_url)
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => $record->slot?->isOnline()),

                        Infolists\Components\TextEntry::make('slot.notes')
                            ->label(__('doctor.clinic_instructions'))
                            ->placeholder(__('doctor.no_clinic_instructions'))
                            ->visible(fn ($record) => $record->slot?->isClinic()),
                    ])
                    ->visible(fn ($record) => $record->slot?->isOnline() || $record->slot?->isClinic()),

                Infolists\Components\Section::make(__('doctor.doctor_notes'))
                    ->schema([
                        Infolists\Components\TextEntry::make('doctor_notes')
                            ->label(__('doctor.private_notes'))
                            ->placeholder(__('doctor.no_private_notes')),
                    ]),

                Infolists\Components\Section::make(__('doctor.booking_history'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('doctor.booked_at'))
                                    ->dateTime('M j, Y g:i A'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('doctor.last_updated'))
                                    ->dateTime('M j, Y g:i A'),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $doctorId = $user?->doctor?->id;

        if (! $doctorId) {
            // Return empty query if no doctor is found
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('doctor_id', $doctorId)
            ->with(['slot', 'doctor.user', 'payment']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Bookings are created by patients, not doctors
    }

    public static function canEdit(Model $record): bool
    {
        $record = $record instanceof Booking ? $record : Booking::find($record->getKey());

        return $record && $record->doctor_id === Auth::user()?->doctor?->id
        && in_array($record->status, ['pending', 'confirmed']);
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Doctors cannot delete bookings
    }

    public static function canView(Model $record): bool
    {
        $record = $record instanceof Booking ? $record : Booking::find($record->getKey());

        return $record && $record->doctor_id === Auth::user()?->doctor?->id;
    }
}
