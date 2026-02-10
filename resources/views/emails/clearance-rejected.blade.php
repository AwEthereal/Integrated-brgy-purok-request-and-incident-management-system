<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Request Update</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            border-left: 4px solid #ef4444;
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
            background: #fee2e2;
            color: #991b1b;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .reason-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3b82f6;
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
        .warning-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="warning-icon">⚠️</div>
        <h1>Purok Clearance Request Update</h1>
        <p style="margin: 10px 0 0 0;">Action required on your purok clearance request</p>
    </div>

    <div class="content">
        @php
            $residentName = optional($request->user)->name ?? ($request->requester_name ?? 'Resident');
            $docLabel = \App\Models\Request::FORM_TYPES[$request->form_type] ?? ucfirst(str_replace('_', ' ', $request->form_type ?? 'Document'));
        @endphp
        <p>Dear <strong>{{ $residentName }}</strong>,</p>

        <p>We regret to inform you that your purok clearance request has been rejected. Please submit the correct information or visit your Purok leader for more information.</p>

        <div style="background: #fee2e2; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #ef4444; text-align: center;">
            <h2 style="margin: 0; color: #991b1b; font-size: 20px;">
                📄 {{ $docLabel }}
            </h2>
            <p style="margin: 10px 0 0 0; color: #b91c1c; font-size: 16px;">
                <strong>Purpose:</strong> {{ $request->purpose }}
            </p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #ef4444;">Request Details</h3>
            <div class="info-row">
                <span class="info-label">Request ID:</span>
                <span class="info-value">#{{ $request->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Document Type:</span>
                <span class="info-value"><strong>Purok Clearance</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Purpose:</span>
                <span class="info-value"><strong>{{ $request->purpose }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value"><span class="status-badge">Rejected</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Reviewed By:</span>
                <span class="info-value">{{ $rejector }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ now()->format('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        @if($reason)
            <div class="reason-box">
                <h4 style="margin-top: 0; color: #991b1b;">📝 Reason:</h4>
                <p style="margin: 0;">{{ $reason }}</p>
            </div>
        @endif

        <h3 style="color: #3b82f6;">What's Next?</h3>
        <p>Please visit the Barangay Hall during office hours to:</p>
        <ul>
            <li>Discuss the concerns with your request</li>
            <li>Contact your Purok leader for more information.</li>
            <li>Resubmit your request with the necessary corrections</li>
        </ul>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/requests') }}" class="button">View My Requests</a>
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
