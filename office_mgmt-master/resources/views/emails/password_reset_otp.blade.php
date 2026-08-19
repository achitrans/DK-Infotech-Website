<div style="font-family: 'Inter', sans-serif; max-width: 600px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
    <p style="margin-bottom: 16px; color: #111827;">Hi {{ $user->name ?? 'there' }},</p>
    <p style="margin-bottom: 12px; color: #4b5563;">We received a request to reset your password for {{ config('app.name') }}.</p>
    <p style="margin-bottom: 12px; font-size: 1.5rem; font-weight: 600;">
        <span style="background: #f3f4f6; padding: 8px 12px; border-radius: 4px; letter-spacing: 2px;">{{ $otp }}</span>
    </p>
    <p style="margin-bottom: 12px; color: #6b7280;">This code expires at {{ $expiresAt->format('h:i A, jS F Y') }}.</p>
    <p style="margin-bottom: 12px; color: #6b7280;">If you did not request a password reset, simply ignore this email and no changes will be made.</p>
    <p style="margin-bottom: 0; color: #111827;">Regards,<br>{{ config('app.name') }} Team</p>
</div>
