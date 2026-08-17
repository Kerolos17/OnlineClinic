<div class="space-y-6">
    @if ($booking->slot?->hasZoomMeeting())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-3">
                <x-heroicon-o-video-camera class="w-5 h-5 text-blue-600" />
                <h3 class="text-lg font-semibold text-blue-900">{{ __('doctor.zoom_details.meeting_details') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('doctor.zoom_details.meeting_id') }}
                    </label>
                    <div class="flex items-center space-x-2">
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $booking->slot->zoom_meeting_id }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $booking->slot->zoom_meeting_id }}')"
                            class="text-blue-600 hover:text-blue-800" title="{{ __('doctor.actions.copy') }}">
                            <x-heroicon-o-clipboard class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('doctor.zoom_details.created_at') }}
                    </label>
                    <p class="text-sm text-gray-600">{{ $booking->slot->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-medium text-green-900 mb-2">{{ __('doctor.zoom_details.start_url') }}</h4>
                <div class="flex items-center space-x-2">
                    <input type="text" value="{{ $booking->slot->zoom_start_url }}" readonly
                        class="flex-1 text-xs bg-white border border-green-300 rounded px-2 py-1">
                    <button onclick="navigator.clipboard.writeText('{{ $booking->slot->zoom_start_url }}')"
                        class="text-green-600 hover:text-green-800" title="{{ __('doctor.actions.copy') }}">
                        <x-heroicon-o-clipboard class="w-4 h-4" />
                    </button>
                </div>
                <a href="{{ $booking->slot->zoom_start_url }}" target="_blank"
                    class="inline-flex items-center mt-2 text-sm text-green-700 hover:text-green-900">
                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 mr-1" />
                    {{ __('doctor.zoom_details.start_meeting') }}
                </a>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">{{ __('doctor.zoom_details.patient_join_url') }}</h4>
                <div class="flex items-center space-x-2">
                    <input type="text" value="{{ $booking->slot->zoom_join_url }}" readonly
                        class="flex-1 text-xs bg-white border border-blue-300 rounded px-2 py-1">
                    <button onclick="navigator.clipboard.writeText('{{ $booking->slot->zoom_join_url }}')"
                        class="text-blue-600 hover:text-blue-800" title="{{ __('doctor.actions.copy') }}">
                        <x-heroicon-o-clipboard class="w-4 h-4" />
                    </button>
                </div>
                <p class="text-xs text-blue-600 mt-2">{{ __('doctor.copy_patient_link') }}</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start space-x-2">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 mt-0.5" />
                <div>
                    <h4 class="font-medium text-yellow-900">{{ __('doctor.zoom_details.important_note') }}</h4>
                    <p class="text-sm text-yellow-800 mt-1">{{ __('doctor.zoom_details.meeting_note') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600" />
                <h3 class="text-lg font-semibold text-red-900">{{ __('doctor.zoom_meeting_missing') }}</h3>
            </div>
            <p class="text-sm text-red-700 mt-2">{{ __('doctor.zoom_api_unavailable') }}</p>

            <div class="mt-4">
                <a href="https://zoom.us/start/webmeeting" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    {{ __('doctor.create_manual_meeting') }}
                </a>
            </div>
        </div>
    @endif
</div>
