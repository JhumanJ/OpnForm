@php
  $config = [
      'appName' => config('app.name'),
      'locale' => $locale = app()->getLocale(),
      'locales' => config('app.locales'),
      'githubAuth' => config('services.github.client_id'),
      'notion' => [
          'worker' => config('services.notion.worker'),
      ],
      'links' => config('links'),
      'production' => App::isProduction(),
      'hCaptchaSiteKey' => config('services.h_captcha.site_key'),
      'google_analytics_code' => config('services.google_analytics_code'),
      'amplitude_code' => config('services.amplitude_code'),
      'crisp_website_id' => config('services.crisp_website_id'),
  ];
@endphp
  <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">
  <link rel="icon" href="{{asset('/img/logo.svg')}}">

  @if($meta)
    <title>{{$meta['title']}}</title>
    <meta name='description' content='{{$meta['description']}}'>

    <meta name='og:title' content='{{$meta['title']}}'>
    <meta name='og:description' content='{{$meta['description']}}'>
    <meta name='og:image' content='{{$meta['image']}}'>
    <meta name='og:site_name' content='OpenForm'>

    <meta name="twitter:title" content="{{$meta['title']}}">
    <meta name="twitter:description" content="{{$meta['description']}}">
    <meta name="twitter:image" content="{{$meta['image']}}">
  @endif

</head>
<body>
<div id="app"></div>

{{-- Global configuration object --}}
<script>
  window.config = @json($config);
  window.$crisp = []
</script>

{{-- Load the application scripts --}}
<script src="{{ mix('dist/js/manifest.js') }}"></script>
<script src="{{ mix('dist/js/vendor.js') }}"></script>
<script src="{{ mix('dist/js/app.js') }}"></script>

@if($config['google_analytics_code'])
<!-- Global site tag (gtag.js) - Google Analytics -->
<script defer src="https://www.googletagmanager.com/gtag/js?id={{ $config['google_analytics_code'] }}"></script>
<script defer>
  window.dataLayer = window.dataLayer || []

  function gtag () {dataLayer.push(arguments)}

  gtag('js', new Date())

  gtag('config', "{{ $config['google_analytics_code'] }}" {{ \Illuminate\Support\Facades\App::isProduction() ? '' : ', { send_page_view: false, debug_mode:true  }' }})
</script>
@endif

</body>
</html>
