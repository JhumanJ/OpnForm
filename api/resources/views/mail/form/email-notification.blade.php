@component('mail::message', ['noBranding' => $noBranding])

{!! $emailContent !!}

@if($form->editable_submissions)
@component('mail::button', ['url' => $form->share_url.'?submission_id='.$submission_id])
{{($form->editable_submissions_button_text ?? 'Edit submission')}}
@endcomponent
@endif

@if($integrationData->include_submission_data)
Here is the answer:

@foreach($fields as $field)
@if(isset($field['value']))

--------------------------------------------------------------------------------

**{{$field['name']}}**

<p style="white-space: pre-wrap">
      {!! is_array($field['value'])?implode(',',$field['value']):$field['value']!!}
</p>

@endif
@endforeach
@endif

@endcomponent