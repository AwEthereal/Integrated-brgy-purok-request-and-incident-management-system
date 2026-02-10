<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Status Update</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
            border-left: 4px solid #3b82f6;
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
        .status-change {
            background: #eff6ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 0 10px;
        }
        .status-old {
            background: #f3f4f6;
            color: #6b7280;
        }
        .status-new {
            background: #dbeafe;
            color: #1e40af;
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
        .update-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="update-icon">📋</div>
        <h1>Status Update</h1>
        <p style="margin: 10px 0 0 0;">Your clearance request status has been updated</p>
    </div>

    <div class="content">
        @php
            $residentName = optional($request->user)->name ?? ($request->requester_name ?? 'Resident');
            $docLabel = \App\Models\Request::FORM_TYPES[$request->form_type] ?? ucfirst(str_replace('_', ' ', $request->form_type ?? 'Document'));
        @endphp
        <p>Dear <strong>{{ $residentName }}</strong>,</p>

        <p>This is to inform you that the status of your purok clearance request has been updated.</p>

        <div style="background: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #3b82f6; text-align: center;">
            <h2 style="margin: 0; color: #1e40af; font-size: 20px;">
                📄 {{ $docLabel }}
            </h2>
            <p style="margin: 10px 0 0 0; color: #1e3a8a; font-size: 16px;">
                <strong>Purpose:</strong> {{ $request->purpose }}
            </p>
        </div>

        <div class="status-change">
            <h3 style="margin-top: 0; color: #3b82f6;">Status Change</h3>
            <div>
                <span class="status-badge status-old">{{ ucfirst($oldStatus) }}</span>
                <span style="font-size: 24px;">→</span>
                <span class="status-badge status-new">{{ ucfirst($newStatus) }}</span>
            </div>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #3b82f6;">Request Details</h3>
            <div class="info-row">
                <span class="info-label">Request ID:</span>
                <span class="info-value">#{{ $request->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Document Type:</span>
                <span class="info-value"><strong>{{ $docLabel }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Purpose:</span>
                <span class="info-value"><strong>{{ $request->purpose }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Status:</span>
                <span class="info-value"><strong>{{ ucfirst($newStatus) }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Updated On:</span>
                <span class="info-value">{{ now()->format('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        @if($newStatus === 'purok_approved')
            <p><strong>✅ Good News!</strong> Your request has been approved by the Purok Leader and forwarded to the Barangay Office.</p>
            <p>The Barangay Office will review your request shortly. You will receive another notification once they process it.</p>
        @elseif($newStatus === 'approved')
            <p><strong>🎉 Congratulations!</strong> Your clearance has been fully approved and is ready for pickup!</p>
            <p>Please visit the Barangay Hall during office hours to claim your document.</p>
        @elseif($newStatus === 'processing')
            <p><strong>⏳ In Progress:</strong> Your request is currently being processed.</p>
            <p>We will notify you once there's an update on your request.</p>
        @else
            <p>Your request status has been updated. Please check your account for more details.</p>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/requests') }}" class="button">View Request Details</a>
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
