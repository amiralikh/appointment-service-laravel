<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmation</title>
</head>
<body>
<h1>Your Appointment is Confirmed</h1>

<p>Dear Customer,</p>

<p>Your appointment has been successfully booked with the following details:</p>

<ul>
    <li><strong>Service:</strong> {{ $serviceName }}</li>
    <li><strong>Health Professional:</strong> {{ $professionalName }}</li>
    <li><strong>Date & Time:</strong> {{ $scheduledAt }}</li>
    <li><strong>Email:</strong> {{ $customerEmail }}</li>
</ul>

<p>If you need to make any changes, please contact us.</p>

<p>Thank you for choosing our services!</p>
</body>
</html>
