<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verify your Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            color: #333333;
        }

        h2 {
            color: #0a2540;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .btn-verify {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 30px;
            background: #4CAF50;
            color: #fff !important;
            font-weight: 600;
            font-size: 18px;
            text-decoration: none;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
            transition: background-color 0.3s ease;
        }

        .btn-verify:hover {
            background: #45a049;
            box-shadow: 0 6px 12px rgba(69, 160, 73, 0.6);
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Hello, {{ $name }}</h2>
        <p>Thank you for registering. Please click the button below to verify your email address:</p>
        <a href="{{ $url }}" class="btn-verify" target="_blank" rel="noopener">Verify Email</a>
        <p class="footer">If you did not register, please ignore this email.</p>
    </div>
</body>

</html>
