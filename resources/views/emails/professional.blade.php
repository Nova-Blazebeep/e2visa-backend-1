<!DOCTYPE html>
<html>

<head>
    <title>Professional Message</title>
</head>

<body>
    <h3>You have received a message</h3>
    <p><strong>Name:</strong> {{ $full_name }}</p>
    <p><strong>Phone Number:</strong> {{ $phone_number }}</p>
    <p><strong>Message:</strong></p>
    <p>{!! nl2br(e($messageBody)) !!}</p>
    <p><strong>Email Adress</strong>: {{$email_adress}}</p>
</body>

</html>
