@extends('emails.layout')

@section('title', 'Verify your Email')

@section('content')
    <h2>Hello, {{ $name }}</h2>
    <p>Thank you for registering with E2Visa. Please confirm your email address to activate your account:</p>

    <a href="{{ $url }}" class="btn" target="_blank" rel="noopener">Verify Email</a>

    <p class="note">If you did not create this account, you can safely ignore this email.</p>
@endsection
