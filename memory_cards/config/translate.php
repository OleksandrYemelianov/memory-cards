<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation service
    |--------------------------------------------------------------------------
    | Supported: "deepl", "" (empty for development without API key)
    */
    'service' => env('TRANSLATE_SERVICE', 'deepl'),

    /*
    |--------------------------------------------------------------------------
    | DeepL API key
    |--------------------------------------------------------------------------
    | Get yours at https://www.deepl.com/pro-api
    */
    'api_key' => env('TRANSLATE_API_KEY', ''),
];
