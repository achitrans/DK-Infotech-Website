<html>

<body>
    Dear {{ $kyc->user->name }}, <br><br>
    @if ($kyc->kyc_status == 'rejected')
        We regret to inform you that your KYC verification has been rejected due to {{ $kyc->remarks }}.
        <br><br>
        Please review the submitted details and re-upload the required documents via our portal: <br>
        🔗 {{ env('APP_URL') }}
        <br><br>
        If you need any assistance or clarification, feel free to contact us.
        <br>
        Thank you for your cooperation. <br><br>
    @else
        We're pleased to inform you that your KYC verification has been successfully approved.
        <br><br>
        You may now proceed with accessing all services and features available through our platform. If you have any
        questions or need assistance, feel free to reach out.
        <br><br>
        Thank you for choosing us. <br><br>
    @endif
    Best regards,<br>
    {{ env('COMPANY_NAME') }}

</body>

</html>
