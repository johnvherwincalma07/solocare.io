<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password OTP</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">

        <h2>Welcome, {{ $user->first_name }}!</h2>

        <p>Your account has been created.</p>

        <p><strong>Username:</strong> {{ $user->username }}</p>
        <p><strong>Password:</strong> {{ $rawPassword }}</p>

        <p> Login here:<a href="{{ url('/login') }}">{{ url('/login') }}</a> </p>

        <p>Please change your password after logging in.</p>
        <p>Thank you,<br>SoloCare Support Team</p>
    </div>
</body>
</html>
