<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
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
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .value {
            color: #333;
            font-size: 16px;
        }
        .message-box {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #0ea5e9;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 New Contact Form Submission</h1>
        </div>
        
        <div class="content">
            <div class="info-row">
                <div class="label">Name</div>
                <div class="value">{{ $contactData['name'] }}</div>
            </div>

            <div class="info-row">
                <div class="label">Email</div>
                <div class="value">
                    <a href="mailto:{{ $contactData['email'] }}" style="color: #0ea5e9; text-decoration: none;">
                        {{ $contactData['email'] }}
                    </a>
                </div>
            </div>

            @if(!empty($contactData['phone']))
            <div class="info-row">
                <div class="label">Phone</div>
                <div class="value">
                    <a href="tel:{{ $contactData['phone'] }}" style="color: #0ea5e9; text-decoration: none;">
                        {{ $contactData['phone'] }}
                    </a>
                </div>
            </div>
            @endif

            <div class="info-row">
                <div class="label">Subject</div>
                <div class="value">{{ $contactData['subject'] }}</div>
            </div>

            <div class="info-row">
                <div class="label">Message</div>
                <div class="message-box">
                    {{ $contactData['message'] }}
                </div>
            </div>

            <div class="info-row">
                <div class="label">Submitted At</div>
                <div class="value">{{ $contactData['submitted_at'] }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from the WellClinic contact form.</p>
            <p style="margin: 5px 0;">© {{ date('Y') }} WellClinic. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
