<!DOCTYPE html>
<html>
<head>
    <title>Verify Email Address</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
        <div style="background-color: #2563eb; color: white; padding: 20px; text-align: center;">
            <h1>Verify Your Email Address</h1>
        </div>
        
        <div style="padding: 20px; border: 1px solid #e5e7eb; border-top: none;">
            <p>Hello!</p>
            <p>Thank you for registering with Kalawag Dos Request System. Please click the button below to verify your email address:</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                    Verify Email Address
                </a>
            </div>
            
            <div style="background-color: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 12px; margin: 16px 0; border-radius: 4px;">
                <p style="margin: 0; color: #0369a1; font-weight: 500;">
                    After clicking the verification link, please refresh your dashboard page to continue.
                </p>
            </div>
            
            <p style="margin-bottom: 16px;">If you did not create an account, no further action is required.</p>
            
            <p style="font-size: 14px; color: #4b5563; margin-bottom: 8px;">
                <strong>Note:</strong> This verification link will expire in 60 minutes.
            </p>
            
            <p>Thanks,<br>Kalawag Dos Request System Team</p>
            
            <div style="margin-top: 20px; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
                <p style="word-break: break-all;">{{ $verificationUrl }}</p>
            </div>
        </div>
    </div>
</body>
</html>
