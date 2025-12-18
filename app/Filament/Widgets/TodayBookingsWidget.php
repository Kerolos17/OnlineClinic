<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Today\'s Bookings')
            ->query(
                Booking::query()
                    ->whereDate('appointment_at', today())
                    ->with(['doctor.user', 'slot'])
                    ->orderBy('appointment_at', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('appointment_at')
                    ->label('Time')
                    ->dateTime('h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.user.name_en')
                    ->label('Doctor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('patient_name')
                    ->label('Patient')
                    ->searchable(),

                Tables\Columns\TextColumn::make('patient_phone')
                    ->label('Phone')
                    ->copyable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\IconColumn::make('zoom_meeting_id')
                    ->label('Zoom')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Booking $record): string => route('filament.admin.resources.bookings.edit', $record)),

                Tables\Actions\Action::make('zoom')
                    ->label('Zoom')
                    ->icon('heroicon-o-video-camera')
                    ->url(fn (Booking $record) => $record->zoom_join_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record) => !empty($record->zoom_join_url))
                    ->color('success'),
            ])
            ->emptyStateHeading('No bookings today')
            ->emptyStateDescription('There are no bookings scheduled for today')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
