<html>
<body>
    <p>Dear {{ $name }},</p>

    <p>We are pleased to offer you the position of <strong>{{ $position }}</strong> at <strong>{{ env('COMPANY_NAME', 'DK Infotech Solutions') }}</strong>. We were highly impressed by your qualifications, skills, and enthusiasm, and we believe you will be a valuable addition to our team.</p>

    <p>Please find attached your formal internship letter, which outlines the terms and conditions of your internship, including the duration, responsibilities, and start date. We kindly request you to review the document carefully.</p>

    <p>To confirm your acceptance of this internship offer, please sign and return the internship letter before the commencement of your internship. Should you have any questions or require further clarification, please feel free to contact us.</p>

    <p>We look forward to welcoming you to {{ env('COMPANY_NAME', 'DK Infotech Solutions') }} and are excited about the contributions you will bring during your internship.</p>

    <p>Best regards,</p>

    <p><strong>{{ env('COMPANY_NAME', 'DK Infotech Solutions') }}</strong></p>
</body>
</html>
