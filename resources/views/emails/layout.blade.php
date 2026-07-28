<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'E2Visa')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f4;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .brand-header {
            background-color: #40433F;
            padding: 26px 40px;
            text-align: center;
        }

        .brand-header .brand-mark {
            display: inline-block;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #ffffff;
            text-decoration: none;
        }

        .brand-header .brand-mark span {
            color: #2EC4B6;
        }

        .content {
            padding: 36px 40px 30px;
            color: #40433F;
        }

        .content h2 {
            margin: 0 0 16px;
            color: #40433F;
            font-size: 22px;
        }

        .content p {
            font-size: 15px;
            line-height: 1.6;
            color: #55584f;
            margin: 0 0 16px;
        }

        .btn {
            display: inline-block;
            margin-top: 12px;
            padding: 14px 32px;
            background-color: #40433F;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(64, 67, 63, 0.3);
        }

        .note {
            margin-top: 26px;
            font-size: 13px;
            color: #9a9a94;
        }

        .divider {
            border: none;
            border-top: 1px solid #eceeec;
            margin: 0;
        }

        .signature {
            padding: 26px 40px 32px;
            background-color: #fafbfa;
        }

        .signature .team {
            font-weight: 700;
            color: #40433F;
            font-size: 14px;
            margin: 0 0 4px;
        }

        .signature .tagline {
            font-size: 12.5px;
            color: #9a9a94;
            font-style: italic;
            margin: 0 0 16px;
        }

        .signature .footer-links {
            font-size: 12px;
            color: #b4b6b0;
            margin: 0;
        }

        .signature .footer-links a {
            color: #2EC4B6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="brand-header">
            <table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 10px;">
                        <img src="{{ $message->embed(public_path('images/logo.png')) }}" width="38" height="35" alt="E2Visa" style="display: block;">
                    </td>
                    <td style="vertical-align: middle;">
                        <span class="brand-mark">E2<span>VISA</span></span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <hr class="divider">

        <div class="signature">
            <p class="team">The E2Visa Team</p>
            <p class="tagline">buy a business, find an immigration attorney, buy a home - all in one place.</p>
            <p class="footer-links">
                <a href="{{ FRONTENDURL }}" target="_blank" rel="noopener">e2visa.com</a>
                &nbsp;&middot;&nbsp;
                &copy; {{ date('Y') }} E2Visa. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
