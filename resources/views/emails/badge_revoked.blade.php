@extends('emails.layout')

@section('title', 'Your Badge Has Been Revoked')

@section('content')
    <h2>Your badge has been revoked, {{ $name }}</h2>
    <p>Your badge is no longer active, and you won't be able to post questions, comments, or replies in the forum until it's reactivated.</p>

    <p style="text-align: center;">
        <a href="{{ FRONTENDURL }}/dashboard?section=settings" class="btn">View Your Account</a>
    </p>

    <p class="note" style="margin-top: 26px;">If you believe this is a mistake, please contact us.</p>
@endsection
