<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Appointment Reminder') }}</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        .alert-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .zoom-button {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            background: #f0f9ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .reminder-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="reminder-icon">⏰</div>
            <h1>{{ __('Appointment Reminder') }}</h1>
        </div>
        
        <div class="content">
            @if($recipient === 'doctor')
                <p>{{ __('Hello') }} <strong>{{ __('Dr.') }} {{ $doctorName }}</strong> 👋</p>
            @else
                <p>{{ __('Hello') }} <strong>{{ $patientName }}</strong> 👋</p>
            @endif
            
            <div class="alert-box">
                <p style="margin: 0; font-weight: 600; color: #92400e;">
                    {{ __('Your appointment is coming up') }} {{ $timeUntil }}!
                </p>
            </div>
            
            <div class="info-box">
                @if($recipient === 'doctor')
                    <p style="margin: 0 0 10px 0; color: #0369a1; font-weight: 600;">
                        {{ __('Appointment with') }} {{ $patientName }}
                    </p>
                @else
                    <p style="margin: 0 0 10px 0; color: #0369a1; font-weight: 600;">
                        {{ __('Appointment with') }} {{ __('Dr.') }} {{ $doctorName }}
                    </p>
                @endif
                <p style="margin: 0; font-size: 18px; font-weight: 700; color: #0c4a6e;">
                    {{ $appointmentTime }}
                </p>
            </div>
            
            <p style="text-align: center;">
                @if($recipient === 'doctor')
                    {{ __('Click the button below to start your Zoom consultation:') }}
                @else
                    {{ __('Click the button below to join your Zoom consultation:') }}
                @endif
            </p>
            
            <div style="text-align: center;">
                <a href="{{ $zoomLink }}" class="zoom-button">
                    🎥 {{ $recipient === 'doctor' ? __('Start Zoom Meeting') : __('Join Zoom Meeting') }}
                </a>
            </div>
            
            <p style="font-size: 14px; color: #64748b;">
                {{ __('Or copy this link:') }}<br>
                <a href="{{ $zoomLink }}" style="color: #0ea5e9; word-break: break-all;">{{ $zoomLink }}</a>
            </p>
            
            <p>{{ __('Please make sure you have a stable internet connection and your camera/microphone are working properly.') }}</p>
            
            <p>{{ __('See you soon!') }}</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} WellClinic. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
