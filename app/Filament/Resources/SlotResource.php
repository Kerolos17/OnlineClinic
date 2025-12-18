<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlotResource\Pages;
use App\Models\Slot;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SlotResource extends Resource
{
    protected static ?string $model = Slot::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Doctor Slots';

    protected static ?string $pluralLabel = 'Slots';

    protected static ?string $modelLabel = 'Slot';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slot Details')
                    ->schema([
                        Forms\Components\Select::make('doctor_id')
                            ->label('Doctor')
                            ->relationship('doctor', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->user->name_en)
                            ->searchable()
                            ->preload()
                            ->placeholder('Select Doctor')
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->displayFormat('Y-m-d')
                            ->minDate(Carbon::today())
                            ->required(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->required()
                                    ->seconds(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->required()
                                    ->seconds(false)
                                    ->after('start_time'),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'available' => 'Available',
                                'booked' => 'Booked',
                            ])
                            ->default('available')
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name_en')
                    ->label('Doctor')
                    ->sortable()
                    ->searchable(['name_en', 'name_ar']),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'booked' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'available' => 'heroicon-o-check-circle',
                        'booked' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date')
                    ->label('Filter by Date')
                    ->form([
                        Forms\Components\DatePicker::make('date')->label('Select Date'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when($data['date'], fn (Builder $query, $date) => $query->whereDate('date', $date)
                    )
                    ),

                SelectFilter::make('doctor_id')
                    ->label('Doctor')
                    ->relationship('doctor.user', 'name_en'),
            ])
            ->defaultSort('date', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlots::route('/'),
            'create' => Pages\CreateSlot::route('/create'),
            'edit' => Pages\EditSlot::route('/{record}/edit'),
        ];
    }
}
