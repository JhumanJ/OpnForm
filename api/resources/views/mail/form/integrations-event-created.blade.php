@component('mail::message')

Hello,

We tried to trigger a **{{$integration_name}}** for your form "{{$form->title}}", but it failed. Here is the error that we got:

@component('mail::panel')
{{$error}}
@endcomponent

Contact us via the website live chat if you need any help.

@endcomponent