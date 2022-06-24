<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Time
    |--------------------------------------------------------------------------
    |
    | Configure how long the sitemap.xml data will be cached.
    |
    | Default = 1 hour (3600 seconds)
    |
    */

    'cache_time' => env('VDLP_SITEMAP_CACHE_TIME', 3600),

    /*
    |--------------------------------------------------------------------------
    | Cache Forever
    |--------------------------------------------------------------------------
    |
    | Cache the sitemap forever.
    |
    */

    'cache_forever' => env('VDLP_SITEMAP_CACHE_FOREVER', false),

];
