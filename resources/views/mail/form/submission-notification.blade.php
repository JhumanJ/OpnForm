@component('mail::message')

Hello there 👋

Your form "{{$form->title}}" has a new submission.

@foreach($fields as $field)
@if(isset($field['value']))

--------------------------------------------------------------------------------

**{{$field['name']}}**

{!! is_array($field['value'])?implode(',',$field['value']):$field['value']!!}

@endif
@endforeach

@endcomponent
