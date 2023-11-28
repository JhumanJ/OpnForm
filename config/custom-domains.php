<?php

return [

    'enabled' => empty(env('CADDY_SECRET')),
    'caddy_secret' => env('CADDY_SECRET'),
    'authorized_ips' => env('CADDY_AUTHORIZED_IPS', []),

];
