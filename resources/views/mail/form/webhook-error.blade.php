@component('mail::message')

Hello,

We tried to trigger a **{{$provider}}** notification for your form "{{$form->title}}", but it failed. Here is the error that we got:

@component('mail::panel')
{{$error}}
@endcomponent

Click [here to edit your form]({{$form->edit_url}}).

Contact us via the website live chat if you need any help.

@endcomponent
