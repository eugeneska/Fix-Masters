<?php

return [

    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID'),
    'ga4_api_secret' => env('GA4_API_SECRET'),
    'ga4_conversion_event' => env('GA4_CONVERSION_EVENT', 'quality_lead'),

    'yandex_metrika_id' => env('YANDEX_METRIKA_ID'),
    'yandex_metrika_oauth_token' => env('YANDEX_METRIKA_OAUTH_TOKEN'),
    'yandex_metrika_goal' => env('YANDEX_METRIKA_GOAL', 'quality_lead'),

    'gtm_container_id' => env('GTM_CONTAINER_ID'),

];
