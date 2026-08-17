<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <x-heroicon-o-video-camera class="w-8 h-8 text-blue-600" />
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-blue-900">{{ __('doctor.zoom_settings.title') }}</h2>
                    <p class="text-sm text-blue-700 mt-1">
                        Configure default settings for your Zoom meetings. These settings will be applied to all new
                        online appointments.
                    </p>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white rounded-lg shadow">
            {{ $this->form }}
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                <x-filament::button wire:click="save" color="primary" icon="heroicon-o-check">
                    {{ __('doctor.zoom_settings.save_settings') }}
                </x-filament::button>

                <x-filament::button wire:click="resetToDefaults" color="gray" icon="heroicon-o-arrow-path"
                    wire:confirm="{{ __('Are you sure you want to reset all settings to defaults?') }}">
                    {{ __('doctor.zoom_settings.reset_to_defaults') }}
                </x-filament::button>
            </div>

            <div class="text-sm text-gray-500">
                <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                Settings are saved automatically for your account
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start space-x-2">
                <x-heroicon-o-light-bulb class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                <div class="text-sm text-yellow-800">
                    <h4 class="font-medium mb-2">Tips for Better Online Consultations:</h4>
                    <ul class="space-y-1 list-disc list-inside">
                        <li>Enable waiting room for patient privacy and security</li>
                        <li>Keep participant video on to maintain personal connection</li>
                        <li>Consider recording sessions for medical records (with patient consent)</li>
                        <li>Use enhanced encryption for sensitive medical discussions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
