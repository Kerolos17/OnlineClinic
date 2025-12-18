<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Booking Confirmation') }}</title>
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
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>{{ __('Booking Confirmed!') }}</h1>
        </div>

        <div class="content">
            @if($recipient === 'doctor')
                <p>{{ __('Hello') }} <strong> {{ $doctorName }}</strong> 👋</p>
                <p>{{ __('You have a new appointment booking. Here are the details:') }}</p>
            @else
                <p>{{ __('Hello') }} <strong>{{ $patientName }}</strong> 👋</p>
                <p>{{ __('Your appointment has been successfully confirmed. Here are your booking details:') }}</p>
            @endif

            <div class="info-box">
                <div class="info-row">
                    <span class="label">{{ __('Booking ID') }}:</span>
                    <span class="value">#{{ $bookingId }}</span>
                </div>
                @if($recipient === 'doctor')
                    <div class="info-row">
                        <span class="label">{{ __('Patient Name') }}:</span>
                        <span class="value">{{ $patientName }}</span>
                    </div>
                @else
                    <div class="info-row">
                        <span class="label">{{ __('Doctor') }}:</span>
                        <span class="value">{{ $doctorName }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="label">{{ __('Specialization') }}:</span>
                    <span class="value">{{ $specialization }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('Appointment Time') }}:</span>
                    <span class="value">{{ $appointmentTime }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('Amount') }}:</span>
                    <span class="value">{{ number_format($amount, 2) }} {{ __('EGP') }}</span>
                </div>
            </div>

            <p>{{ __('You will receive a Zoom meeting link via email 30 minutes before the appointment.') }}</p>

            @if($recipient === 'patient')
                <p>{{ __('If you need to cancel or reschedule, please contact us as soon as possible.') }}</p>
                <p>{{ __('Thank you for choosing WellClinic!') }}</p>
            @else
                <p>{{ __('Please be ready to join the consultation at the scheduled time.') }}</p>
            @endif
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} WellClinic. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
