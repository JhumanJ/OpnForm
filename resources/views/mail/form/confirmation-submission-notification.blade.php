@component('mail::message', ['noBranding' => $noBranding])

{!! $integrationData->notification_body !!}

@if($form->editable_submissions)
@component('mail::button', ['url' => $form->share_url.'?submission_id='.$submission_id])
{{($form->editable_submissions_button_text ?? 'Edit submission')}}
@endcomponent
@endif

@if($integrationData->notifications_include_submission)
As a reminder, here are your answers:

@foreach($fields as $field)
@if(isset($field['value']))

--------------------------------------------------------------------------------

**{{$field['name']}}**
@if($field['type'] == 'files')
<br />
@foreach($field['email_data'] as $link)
<a href="{{$link['signed_url']}}">{{$link['label']}}</a> <br />
@endforeach
@else
{!! is_array($field['value'])?implode(',',$field['value']):$field['value']!!}
@endif
@endif
@endforeach
@endif

<p style="text-align:center"><small>You are receiving this email because you answered the form: <a href="{{front_url("forms/".$form->slug)}}">"{{$form->title}}"</a>.</small></p>

@endcomponent