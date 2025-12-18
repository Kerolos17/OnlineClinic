<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->getDisplayName())
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->searchable(),
                Forms\Components\TextInput::make('patient_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('patient_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('rating')
                    ->options([
                        1 => '⭐ 1 Star',
                        2 => '⭐⭐ 2 Stars',
                        3 => '⭐⭐⭐ 3 Stars',
                        4 => '⭐⭐⭐⭐ 4 Stars',
                        5 => '⭐⭐⭐⭐⭐ 5 Stars',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Doctor')
                    ->formatStateUsing(fn ($record) => $record->doctor->user->getDisplayName())
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (Review $record) => $record->status !== 'approved'),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn (Review $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (Review $record) => $record->status !== 'rejected'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
