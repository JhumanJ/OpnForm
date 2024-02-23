<?php

return [

    'enabled' => ! empty(env('CADDY_SECRET')) && ! empty(env('CADDY_AUTHORIZED_IPS', [])),
    'caddy_secret' => env('CADDY_SECRET'),
    'authorized_ips' => explode(',', env('CADDY_AUTHORIZED_IPS', '')),

];
