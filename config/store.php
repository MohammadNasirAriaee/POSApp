<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store Details
    |--------------------------------------------------------------------------
    |
    | Printed at the top of every customer receipt. The name falls back to the
    | application name; address and phone are omitted from the receipt when
    | left blank, so a new install prints no placeholder contact details.
    |
    */

    // `?:` rather than an env() default: a key present but blank in .env reads
    // as an empty string, which should still fall back.
    'name' => env('STORE_NAME') ?: env('APP_NAME', 'POSApp'),

    'address' => env('STORE_ADDRESS') ?: null,

    'phone' => env('STORE_PHONE') ?: null,

];
