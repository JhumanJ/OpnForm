<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JSON Source URL
    |--------------------------------------------------------------------------
    |
    | The source URL yielding a list of disposable email domains. Change this
    | to whatever source you like. Just make sure it returns a JSON array.
    |
    | A sensible default is provided using jsDelivr's services. jsDelivr is
    | a free service, so there are no uptime or support guarantees.
    |
    */

    'source' => 'https://cdn.jsdelivr.net/gh/disposable/disposable-email-domains@master/domains.json',

    /*
    |--------------------------------------------------------------------------
    | Fetch class
    |--------------------------------------------------------------------------
    |
    | The class responsible for fetching the contents of the source url.
    | The default implementation makes use of file_get_contents and
    | json_decode and will probably suffice for most applications.
    |
    | If your application has different needs (e.g. behind a proxy) then you
    | can define a custom fetch class here that carries out the fetching.
    | Your custom class should implement the Fetcher contract.
    |
    */

    'fetcher' => \Propaganistas\LaravelDisposableEmail\Fetcher\DefaultFetcher::class,

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The location where the retrieved domains list should be stored locally.
    | The path should be accessible and writable by the web server. A good
    | place for storing the list is in the framework's own storage path.
    |
    */

    'storage' => storage_path('framework/disposable_domains.json'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define whether the disposable domains list should be cached.
    | If you disable caching or when the cache is empty, the list will be
    | fetched from local storage instead.
    |
    | You can optionally specify an alternate cache connection or modify the
    | cache key as desired.
    |
    */

    'cache' => [
        'enabled' => true,
        'store' => 'default',
        'key' => 'disposable_email:domains',
    ],

];
