<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password OTP</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #003366;">SoloCare Password Reset</h2>
        <p>Hello,</p>
        <p>You requested a password reset. Use the OTP below to reset your password. It will expire in 10 minutes.</p>
        <h3 style="color: #0055a5; text-align: center;">{{ $otp }}</h3>
        <p>If you did not request this, please ignore this email.</p>
        <p>Thank you,<br>SoloCare Support Team</p>
    </div>
</body>
</html>
