<html>
<body>

    Dear {{ $user->name }}, <br>
    <br>
    @if ($user->isClient())
        Welcome to {{ env('COMPANY_NAME')}} - we're excited to have you onboard! <br>
        As a client, you play a crucial role in our journey, and we are committed to providing you with exceptional service and support. <br>
        Your account has been successfully created. Below are your login details to get started: <br>
    @endif

    @if ($user->isEmployee())
    Welcome to {{ env('COMPANY_NAME')}}! We are excited to have you as a part of our growing team. Your skills and talents
    will be a valuable asset to our organization, and we look forward to achieving great things together.<br>
    @endif
    
    <br>
    To help you get started, please find your login details below:<br>
    <br>
    Login Portal: {{ env('APP_URL') }} <br>
    Username: {{ $user->email }} <br>
    Temporary Password: {{ env('DEFAULT_PASSWORD') }} <br>
    <br>
    Note: For security reasons, please change your password as soon as possbile & upon your first login.<br>
    <br>

    @if ($user->isEmployee())
    We are committed to making your onboarding experience smooth and welcoming. Your supervisor will be reaching out
    shortly with further steps and introductions.<br>
    <br>
    Once again, welcome to the team!<br>
    @endif

    @if($user->isClient())
        Our team is here to support you every step of the way. If you have any questions or need assistance, please feel free to reach out to us at {{ env('COMPANY_EMAIL')}} or {{ env('COMPANY_PHONE')}}. <br>
        We look forward to helping you grow with smart, tailored tech solutions. <br>
    @endif

    <br>
    Warm regards,<br>
    {{ env('COMPANY_NAME') }}<br>
    {{ env('COMPANY_EMAIL') }}<br>
    {{ env('COMPANY_PHONE') }}<br>
    {{ env('COMPANY_ADDRESS') }}<br>
    {{ env('COMPANY_WEBSITE') }}<br>


</body>

</html>
