<?php

return [

    /*
    |--------------------------------------------------------------------------
    | databinding
    | Specify the start and end tokens of the databinding values.
    | Translationcaptain needs to know how to recognize text in the translations that will be bound to a value later
    | standard laravel: 'You have purchased :itemcount items'.
    | This can be translated into 'Je hebt :itemcount dingen gekocht.
    | Itemcount cannot be translated as this is the value from the code. Translationcaptain needs to know this.
    | Any recognized databinding in vue and laravel are transformed for Translationcaptain into something like
    | 'You purchased %%itemcount%% products
    |--------------------------------------------------------------------------
    |
    */

    'databinding' => [
        'start' => "%%",
        'end' => "%%",
    ],

    'paths' => [
        'vue' => '/resources/js/components/lang',
        'blade' => '/resources/lang',

        ],

    'paths_sources' => [
        'blade' => [
            '/resources/views/bedrock/admin'
            //'/app/',
            //'/config/',
        ]

    ],

    'group_when_group_missing' => '___',

    'exceptions' => [
        'on_missing_key' => true
    ],

    'default_locale' => env('DEFAULT_LOCALE', 'nl'),

    'log_missing_keys' => env('TRANS_LOG_MISSING', true),

    'queue_filename' => 'translationcaption-queue.log'

];
