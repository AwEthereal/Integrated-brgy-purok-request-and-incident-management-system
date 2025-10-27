<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f3f4f6;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 0 !important;
            }
            .header, .content, .footer {
                padding: 20px !important;
            }
            .info-row {
                flex-direction: column;
                padding: 10px 0 !important;
            }
            .info-label {
                width: 100% !important;
                margin-bottom: 5px;
            }
            h1 {
                font-size: 20px !important;
            }
            h2 {
                font-size: 18px !important;
            }
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            word-wrap: break-word;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #6b7280;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
            color: #111827;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            margin-top: 20px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="success-icon">✅</div>
        <h1>Clearance Request Approved!</h1>
        <p style="margin: 10px 0 0 0;">Your purok clearance request has been approved</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $request->user->full_name }}</strong>,</p>

        <p>We are pleased to inform you that your purok clearance request has been <strong>approved</strong>.</p>

        <div style="background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #10b981; text-align: center;">
            <h2 style="margin: 0; color: #065f46; font-size: 20px;">
                📄 {{ ucfirst(str_replace('_', ' ', $request->document_type)) }}
            </h2>
            <p style="margin: 10px 0 0 0; color: #047857; font-size: 16px;">
                <strong>Purpose:</strong> {{ $request->purpose }}
            </p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #10b981;">Request Details</h3>
            <div class="info-row">
                <span class="info-label">Request ID:</span>
                <span class="info-value">#{{ $request->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Document Type:</span>
                <span class="info-value"><strong>{{ ucfirst(str_replace('_', ' ', $request->document_type)) }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Purpose:</span>
                <span class="info-value"><strong>{{ $request->purpose }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value"><span class="status-badge">{{ ucfirst($request->status) }}</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Approved By:</span>
                <span class="info-value">{{ $approver }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Approved On:</span>
                <span class="info-value">{{ now()->format('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        @if($request->status === 'barangay_approved')
            <p><strong>🎉 Your document is now ready for pickup!</strong></p>
            <p>Please visit the Barangay Hall during office hours to claim your <strong>{{ ucfirst(str_replace('_', ' ', $request->document_type)) }}</strong>.</p>
        @elseif($request->status === 'purok_approved')
            <p><strong>✅ Next Step: Pick up your Purok Clearance</strong></p>
            <p>Your request has been approved by the Purok President. Please visit the <strong>Purok President's residence</strong> during office hours to pick up your <strong>Purok Clearance</strong>.</p>
            <p><strong>📍 After getting your Purok Clearance:</strong></p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Bring the Purok Clearance to the Barangay Hall</li>
                <li>Submit it for final processing of your <strong>{{ ucfirst(str_replace('_', ' ', $request->document_type)) }}</strong></li>
                <li>The Barangay Office will review and approve your request</li>
            </ul>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/requests') }}" class="button">View Request Status</a>
        </div>

        <p style="color: #6b7280; font-size: 14px;">
            <strong>Office Hours:</strong><br>
            Monday - Friday: 8:00 AM - 5:00 PM<br>
            Saturday: 8:00 AM - 12:00 PM
        </p>
    </div>

    <div class="footer">
        <p><strong>Barangay Kalawag II</strong><br>
        General Siongco Street, Isulan, Sultan Kudarat</p>
        <p style="font-size: 12px; color: #9ca3af;">
            This is an automated email. Please do not reply to this message.<br>
            For inquiries, please visit the Barangay Hall or contact us during office hours.
        </p>
    </div>
</body>
</html>
