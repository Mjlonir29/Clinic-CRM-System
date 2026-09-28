<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ekta Care Clinic CRM - Password Reset OTP</title>
</head>
<body style="font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 36px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="display: inline-block; width: 48px; height: 48px; background-color: #006654; border-radius: 14px; text-align: center; line-height: 48px; color: #ffffff; font-size: 24px; font-weight: bold; margin-bottom: 12px;">
                +
            </div>
            <h1 style="color: #0f172a; font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">Ekta Care Clinic CRM</h1>
            <p style="color: #64748b; font-size: 12px; font-weight: 600; margin-top: 4px;">Secure Account Password Recovery</p>
        </div>

        <!-- Greeting -->
        <p style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 16px;">
            Hello,
        </p>
        <p style="font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px;">
            We received a request to reset your password for your account linked to <strong style="color: #0f172a;">{{ $email }}</strong>.
        </p>

        <!-- OTP Highlight Box -->
        <div style="background-color: #f0fdf9; border: 1.5px dashed #14b8a6; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 24px;">
            <p style="margin: 0; font-size: 12px; color: #005243; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Your 6-Digit Verification Code</p>
            <div style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #006654; margin: 12px 0; font-family: 'Courier New', Courier, monospace;">
                {{ $otp }}
            </div>
            <p style="margin: 0; font-size: 11px; color: #003e33; font-weight: 600;">⏱️ Code expires in 15 minutes</p>
        </div>

        <p style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; text-align: center;">
            Enter this 6-digit code on the OTP verification page to set your new password.
        </p>

        <!-- Footer -->
        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center; font-size: 11px; color: #94a3b8; line-height: 1.5;">
            If you did not request a password reset, you can safely ignore this email.<br>
            © {{ date('Y') }} Ekta Care Clinic CRM. All rights reserved.
        </div>

    </div>
</body>
</html>
