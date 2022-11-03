<x-mail::layout>
{{-- Header --}}
<x-slot:header>
@if (!(isset($noBranding) && $noBranding))
    <x-mail::header :url="config('app.url')">
    {{ config('app.name') }}
    </x-mail::header>
@else
    <div style="margin-top:25px;" />
@endif
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
@if (!(isset($noBranding) && $noBranding))
    © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endif
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
