@extends('emails.layout')

@section('title', 'New Message')

@section('content')
    <h2>You have a new message</h2>
    <p>Someone reached out to you through your E2Visa profile:</p>

    <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 18px 0; font-size: 15px; color: #40433F;">
        <tr>
            <td style="padding: 6px 0; width: 120px; color: #9a9a94;">Name</td>
            <td style="padding: 6px 0; font-weight: 600;">{{ $full_name }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #9a9a94;">Phone</td>
            <td style="padding: 6px 0; font-weight: 600;">{{ $phone_number }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #9a9a94;">Email</td>
            <td style="padding: 6px 0; font-weight: 600;">{{ $email_adress }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 6px;"><strong>Message:</strong></p>
    <p style="background: #f4f5f4; padding: 14px 18px; border-radius: 8px;">{!! nl2br(e($messageBody)) !!}</p>
@endsection
