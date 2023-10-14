@component('mail::message')

Hey there 👋

Congrats! Your form "{{$form->title}}" was successfully created!

If you want to share your form, here is the link:

@component('mail::panel')
{{$form->share_url}}
@endcomponent

If you want to **embed your form in Notion**, simply type "/embed", confirm, and paste the URL above.
If you want to add the form to your website, copy and paste the following code:
@component('mail::panel')
 {{ '<iframe style="border:none;width:100%;" height="620px" src="'.$form->share_url.'"></iframe>' }}
@endcomponent

Finally, we created a **Facebook group** with all the other users to share all the exciting news and tutorials about NotionForms.
I would love to see you there, here is the link:

@component('mail::button', ['url' => config('links.facebook_group')])
Join Facebook Group
@endcomponent

@endcomponent
