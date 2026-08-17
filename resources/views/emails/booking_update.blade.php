<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'cancellation' ? 'Appointment Cancelled' : ($type === 'zoom_update' ? 'Zoom Meeting Updated' : 'Appointment Updated') }}</title>
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif' }};
            background-color: #f0f9ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0f2fe;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #0369a1;
        }
        .value {
            color: #0c4a6e;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .changes-list {
            margin: 10px 0;
            padding: 0;
            list-style: none;
        }
        .changes-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e0f2fe;
            color: #0c4a6e;
        }
        .button {
            display: inline-block;
            background: #0ea5e9;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">
                @if($type === 'cancellation') ❌
                @elseif($type === 'zoom_update') 🔗
                @else ✏️
                @endif
            </div>
            <h1>
                @if($type === 'cancellation')
                    {{ __('messages.Appointment Cancelled') }}
                @elseif($type === 'zoom_update')
                    {{ __('messages.Zoom Meeting Updated') }}
                @else
                    {{ __('messages.Appointment Updated') }}
                @endif
            </h1>
        </div>

        <div class="content">
            <p>{{ __('messages.Hello') }} <strong>{{ $patientName }}</strong> 👋</p>

            @if($type === 'cancellation')
                <p>{{ __('messages.We are sorry to inform you that your appointment has been cancelled.') }}</p>
            @elseif($type === 'modification')
                <p>{{ __('messages.Your appointment details have been updated. Here are the changes:') }}</p>
                @if($changes && count($changes))
                    <ul class="changes-list">
                        @foreach($changes as $field => $change)
                            <li>
                                <strong>{{ is_string($field) ? $field : '' }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @else
                <p>{{ __('messages.Your Zoom meeting link has been updated.') }}</p>
                <p>{{ __('messages.Click the button below to join your consultation:') }}</p>
            @endif

            <div class="info-box">
                <div class="info-row">
                    <span class="label">{{ __('messages.Appointment with') }}:</span>
                    <span class="value">{{ $doctorName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('messages.Appointment Time') }}:</span>
                    <span class="value">{{ $appointmentTime }}</span>
                </div>
                @if($type === 'zoom_update' && $zoomJoinUrl)
                    <div class="info-row">
                        <span class="label">{{ __('messages.Join Zoom Meeting') }}:</span>
                        <span class="value"><a href="{{ $zoomJoinUrl }}" class="button">{{ __('messages.Join Zoom Meeting') }}</a></span>
                    </div>
                @endif
            </div>

            @if($type === 'cancellation')
                <p>{{ __('messages.If you have any questions, please contact us.') }}</p>
                <p>{{ __('messages.Thank you for choosing WellClinic!') }}</p>
            @else
                <p>{{ __('messages.Thank you for choosing WellClinic!') }}</p>
            @endif
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} WellClinic. {{ __('messages.All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
