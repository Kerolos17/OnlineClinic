<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->label('Doctor')
                    ->relationship('doctor', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name_en ?? $record->user->email)
                    ->searchable(['name_en', 'name_ar'])
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset slot when doctor changes
                        $set('slot_id', null);
                    }),
                Forms\Components\Select::make('slot_id')
                    ->label('Time Slot')
                    ->relationship('slot', 'id', function ($query, $get) {
                        // Only show available slots for selected doctor
                        $doctorId = $get('doctor_id');
                        if ($doctorId) {
                            return $query->where('doctor_id', $doctorId)
                                ->where('status', 'available')
                                ->orderBy('date')
                                ->orderBy('start_time');
                        }
                        return $query->where('status', 'available');
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->date->format('Y-m-d').' | '.$record->start_time.' - '.$record->end_time)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn ($get) => !$get('doctor_id'))
                    ->helperText('Select a doctor first'),
                Forms\Components\Section::make('Patient Information')
                    ->schema([
                        Forms\Components\TextInput::make('patient_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('patient_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('patient_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('patient_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\DateTimePicker::make('appointment_at')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Zoom Meeting')
                    ->schema([
                        Forms\Components\TextInput::make('zoom_meeting_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zoom_join_url')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('zoom_start_url')
                            ->url()
                            ->maxLength(500),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name_en')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('patient_phone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EGY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('zoom_meeting_id')
                    ->label('Zoom')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctor.user', 'name_en'),
                Tables\Filters\Filter::make('appointment_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('zoom_link')
                    ->label('Zoom')
                    ->icon('heroicon-o-video-camera')
                    ->url(fn (Booking $record) => $record->zoom_join_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record) => ! empty($record->zoom_join_url)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('appointment_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
