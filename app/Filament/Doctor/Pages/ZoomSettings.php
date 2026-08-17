<?php

namespace App\Filament\Doctor\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * @property-read \Filament\Forms\Form $form
 */
class ZoomSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.doctor.pages.zoom-settings';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'zoom-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('doctor.zoom_settings.title');
    }

    public function getTitle(): string
    {
        return __('doctor.zoom_settings.title');
    }

    public function mount(): void
    {
        $this->form->fill($this->getDefaultSettings());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('doctor.zoom_settings.security_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('waiting_room')
                            ->label(__('doctor.zoom_settings.waiting_room'))
                            ->helperText(__('doctor.zoom_settings.waiting_room_help'))
                            ->default(true),

                        Forms\Components\Toggle::make('password_protection')
                            ->label(__('doctor.zoom_settings.password_protection'))
                            ->helperText(__('doctor.zoom_settings.password_protection_help'))
                            ->default(false),

                        Forms\Components\Toggle::make('encryption')
                            ->label(__('doctor.zoom_settings.encryption'))
                            ->helperText(__('doctor.zoom_settings.encryption_help'))
                            ->default(true),

                        Forms\Components\Select::make('approval_type')
                            ->label(__('doctor.zoom_settings.approval_type'))
                            ->options([
                                0 => __('doctor.zoom_settings.approval_automatic'),
                                1 => __('doctor.zoom_settings.approval_manual'),
                                2 => __('doctor.zoom_settings.approval_none'),
                            ])
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('doctor.zoom_settings.recording'))
                    ->schema([
                        Forms\Components\Select::make('auto_recording')
                            ->label(__('doctor.zoom_settings.recording'))
                            ->helperText(__('doctor.zoom_settings.recording_help'))
                            ->options([
                                'none' => __('doctor.zoom_settings.recording_none'),
                                'local' => __('doctor.zoom_settings.recording_local'),
                                'cloud' => __('doctor.zoom_settings.recording_cloud'),
                            ])
                            ->default('none'),
                    ]),

                Forms\Components\Section::make('Audio & Video Settings')
                    ->schema([
                        Forms\Components\Toggle::make('video_host')
                            ->label(__('doctor.zoom_settings.video_host'))
                            ->helperText(__('doctor.zoom_settings.video_host_help'))
                            ->default(true),

                        Forms\Components\Toggle::make('video_participants')
                            ->label(__('doctor.zoom_settings.video_participants'))
                            ->helperText(__('doctor.zoom_settings.video_participants_help'))
                            ->default(true),

                        Forms\Components\Toggle::make('mute_upon_entry')
                            ->label(__('doctor.zoom_settings.mute_participants'))
                            ->helperText(__('doctor.zoom_settings.mute_participants_help'))
                            ->default(false),

                        Forms\Components\Toggle::make('join_before_host')
                            ->label(__('doctor.zoom_settings.join_before_host'))
                            ->helperText(__('doctor.zoom_settings.join_before_host_help'))
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Meeting Duration')
                    ->schema([
                        Forms\Components\TextInput::make('meeting_duration')
                            ->label(__('doctor.zoom_settings.meeting_duration'))
                            ->helperText(__('doctor.zoom_settings.meeting_duration_help'))
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(240)
                            ->default(30)
                            ->suffix('minutes'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        try {
            // Save settings to cache for this doctor
            $cacheKey = 'zoom_settings_doctor_'.Auth::user()->doctor->id;
            Cache::put($cacheKey, $data, now()->addDays(30));

            Notification::make()
                ->title(__('doctor.zoom_settings.settings_saved'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to save settings: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetToDefaults(): void
    {
        $this->form->fill($this->getDefaultSettings());

        // Clear cached settings
        $cacheKey = 'zoom_settings_doctor_'.Auth::user()->doctor->id;
        Cache::forget($cacheKey);

        Notification::make()
            ->title(__('doctor.zoom_settings.settings_reset'))
            ->success()
            ->send();
    }

    protected function getDefaultSettings(): array
    {
        $cacheKey = 'zoom_settings_doctor_'.Auth::user()->doctor->id;

        return Cache::get($cacheKey, [
            'waiting_room' => true,
            'password_protection' => false,
            'encryption' => true,
            'approval_type' => 0,
            'auto_recording' => 'none',
            'video_host' => true,
            'video_participants' => true,
            'mute_upon_entry' => false,
            'join_before_host' => true,
            'meeting_duration' => 30,
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label(__('doctor.zoom_settings.save_settings'))
                ->submit('save')
                ->color('primary'),

            Forms\Components\Actions\Action::make('reset')
                ->label(__('doctor.zoom_settings.reset_to_defaults'))
                ->action('resetToDefaults')
                ->color('gray')
                ->requiresConfirmation(),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->doctor !== null;
    }
}
