@extends('emails.layout')

@section('title', 'Your Badge Is Active')

@section('content')
    <h2>Your badge is active, {{ $name }}!</h2>
    <p>Good news — we've confirmed your payment and activated your badge. You can now post questions, comments, and replies in the forum.</p>

    <p style="text-align: center;">
        <a href="{{ FRONTENDURL }}/forum" class="btn">Visit the Forum</a>
    </p>

    <p class="note" style="margin-top: 26px;">You can review your badge status anytime from your account Settings page.</p>
@endsection
