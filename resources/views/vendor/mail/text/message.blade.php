<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        @if (!(isset($noBranding) && $noBranding))
            <x-mail::header :url="config('app.url')">
                {{ config('app.name') }}
            </x-mail::header>
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
        @if (!(isset($noBranding) && $noBranding))
            <x-mail::footer>
                © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
            </x-mail::footer>
        @endif
    </x-slot:footer>
</x-mail::layout>
