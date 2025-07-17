@component('mail::message')
# Your Account Has Been Unblocked

Hello {{ $user->name }},

Your account has been unblocked by an administrator. You can now log in to your account.

**Reason for unblocking:**
{{ $reason }}

If you have any questions, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent