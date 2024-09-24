@component('mail::message')

Hello there 👋

Your form "{{$form->title}}" has a new submission.

@foreach($fields as $field)
@if(isset($field['value']))

--------------------------------------------------------------------------------

**{{$field['name']}}**
@if($field['type'] == 'files')
<br/>
@foreach($field['email_data'] as $link)
<a href="{{$link['signed_url']}}">{{$link['label']}}</a> <br/>
@endforeach
@else
@if($field['type'] == 'matrix')
{!! nl2br(e($field['value'])) !!}
@else
{!! is_array($field['value'])?implode(',',$field['value']):$field['value']!!}
@endif
@endif
@endif
@endforeach

@endcomponent
