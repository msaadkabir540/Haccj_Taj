@component('mail::message')
# Password Reset Request

We have received a request to reset your password. Please enter the following OTP to continue with the password reset process.

<h3 style="text-align: center;">{{ $otp }}</h3>

Thanks,<br>
{{ config('app.name') }}
@endcomponent