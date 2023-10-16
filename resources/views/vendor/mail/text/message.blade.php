@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @if (!(isset($noBranding) && $noBranding))
            @component('mail::header', ['url' => config('app.url')])
                {{ config('app.name') }}
            @endcomponent
        @endif
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            @if (!(isset($noBranding) && $noBranding))
                © {{ date('Y') }} {{ config('app.name') }}
            @endif
        @endcomponent
    @endslot
@endcomponent
