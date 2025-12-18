<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Clinic Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'email')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_en ?? $record->email)
                    ->label('User (Doctor)')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name_en')
                            ->label('Name (English)')
                            ->required(),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('Name (Arabic)')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email')
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->label('Password')
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->options(['doctor' => 'Doctor'])
                            ->default('doctor')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone'),
                    ]),

                Forms\Components\Select::make('specialization_id')
                    ->relationship('specialization', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_en)
                    ->label('Specialization')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Textarea::make('bio.en')
                    ->label('Bio (English)')
                    ->rows(3),

                Forms\Components\Textarea::make('bio.ar')
                    ->label('Bio (Arabic)')
                    ->rows(3),

                Forms\Components\TextInput::make('experience_years')
                    ->numeric()
                    ->label('Experience (Years)')
                    ->required()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\TagsInput::make('languages')
                    ->placeholder('Add languages')
                    ->label('Languages')
                    ->required(),

                Forms\Components\TextInput::make('consultation_price')
                    ->numeric()
                    ->prefix('$')
                    ->label('Consultation Price')
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->label('Profile Image')
                    ->image()
                    ->directory('doctors')
                    ->imageEditor()
                    ->disk('public')
                    ->getUploadedFileNameForStorageUsing(fn ($file) => (string) str()->uuid().'.'.$file->getClientOriginalExtension()),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->path('doctors')
                    ->circular()
                    ->label('Photo'),

                Tables\Columns\TextColumn::make('user.name_en')
                    ->label('Doctor Name')
                    ->searchable(['name_en', 'name_ar'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('specialization.name_en')
                    ->label('Specialization')
                    ->searchable(['name_en', 'name_ar'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('experience_years')
                    ->label('Experience')
                    ->suffix(' years')
                    ->sortable(),

                Tables\Columns\TextColumn::make('consultation_price')
                    ->money('EGP')
                    ->label('Consultation Fee')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->label('Rating')
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('specialization')
                    ->relationship('specialization', 'name_en')
                    ->label('Filter by Specialization'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
