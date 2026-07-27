@extends('emails.layout')

@section('title', 'Your New Password')

@section('content')
    <h2>Hello, {{ $user->name }}</h2>
    <p>We've generated a new password for your account as requested.</p>

    <p style="margin-bottom: 6px;"><strong>Your new password:</strong></p>
    <p style="font-size: 22px; font-weight: 700; color: #40433F; letter-spacing: 0.5px; background: #f4f5f4; padding: 12px 18px; border-radius: 8px; display: inline-block;">
        {{ $password }}
    </p>

    <p style="margin-top: 20px;">Please log in with this password and change it right away for security.</p>

    <p class="note">If you did not request a password reset, please contact us immediately.</p>
@endsection
