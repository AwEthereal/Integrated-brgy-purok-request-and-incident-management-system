<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Test Email from Kalawag System</h2>
        
        <p>Hello {{ $userName }},</p>
        
        <p>This is a test email to verify the email system is working correctly.</p>
        
        <p><strong>Request ID:</strong> #{{ $requestId }}</p>
        
        <p>If you received this email, the email system is working!</p>
        
        <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
        
        <p style="font-size: 12px; color: #6b7280;">
            This is an automated test email from Barangay Kalawag Dos Request System.
        </p>
    </div>
</body>
</html>
