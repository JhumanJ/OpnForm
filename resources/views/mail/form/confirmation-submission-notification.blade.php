@component('mail::message', ['noBranding' => $noBranding])

{!! $form->notification_body !!}

@if($form->editable_submissions)
@component('mail::button', ['url' => $form->share_url.'?submission_id='.$submission_id])
{{($form->editable_submissions_button_text ?? 'Edit submission')}}
@endcomponent
@endif

@if($form->notifications_include_submission)
As a reminder, here are your answers:

@foreach($fields as $field)
@if(isset($field['value']))

--------------------------------------------------------------------------------

**{{$field['name']}}**

{!! is_array($field['value'])?implode(',',$field['value']):$field['value']!!}

@endif
@endforeach
@endif

<p style="text-align:center"><small>You are receiving this email because you answered the form: <a href="{{url("forms/".$form->slug)}}">"{{$form->title}}"</a>.</small></p>

@endcomponent
