<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => '42227846360-ejslm7jf2orv42isq9q1m5e027t2qfbc.apps.googleusercontent.com',
        'client_secret' =>'lFOdrbzGbGFTu_BLCg0NN4p7',
        'redirect' => 'http://localhost:8000/api/ValidateByGoogle',
    ],

    'facebook' => [
        'client_id' => '1342323862830933',
        'client_secret' => '778341ee4e3107a033f9f07a0c13ee18',
        'redirect' => 'http://localhost:8000/api/ValidateByFaceBook',
    ],
];
