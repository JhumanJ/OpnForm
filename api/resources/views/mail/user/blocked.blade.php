@component('mail::message')
# Your Account Has Been Blocked

Hello {{ $user->name }},

Your account has been blocked by an administrator.

**Reason:**
{{ $reason }}

If you believe this is a mistake, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent