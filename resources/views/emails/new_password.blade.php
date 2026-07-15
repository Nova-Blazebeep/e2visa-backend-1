<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your New Password</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>We have generated a new password for you as per your request.</p>

    <p><strong>Your new password is:</strong></p>
    <p style="font-size: 20px; font-weight: bold; color: #2c3e50;">{{ $password }}</p>

    <p>Please use this password to log in, and don't forget to change it after logging in for security reasons.</p>

    <br>
    <p>Best regards,</p>
    <p>E2Visa</p>
</body>
</html>
