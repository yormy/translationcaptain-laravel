<?php

use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderBlade;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderVue;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled or not
    |--------------------------------------------------------------------------
    | Enable or disable the entire TranslationCaptain functionality
    | When disabled, no keys are collected, no context is pushed,
    | no exception on missing keys
    |--------------------------------------------------------------------------
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Translation captain project id
    |--------------------------------------------------------------------------
    | The id of the project on TranslationCaptain for which these keys are used
    */
    'project_id' => 'IEB3rcW3SzeOEANm7LEi6w',

    /*
    |--------------------------------------------------------------------------
    | TranslationCaptain push/pull/screenshot url
    |--------------------------------------------------------------------------
    |
    */
    'url' => 'http://localhost/api/v1/translationcaptain', // 'https://backend.bedrock.local/
    //'url' => 'https://backend.bedrock.local/api/v1/translationcaptain',


    /*
    |--------------------------------------------------------------------------
    | Group name of the key if there was no group name found
    |--------------------------------------------------------------------------
    |
    */
    'group_when_group_missing' => '___',

    /*
    |--------------------------------------------------------------------------
    | Throw exception when missing key found
    |--------------------------------------------------------------------------
    |
    */
    'exceptions' => [
        'on_missing_key' => false
    ],




    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    | This is de default base locale. New translation lines stored in the database will
    | automatically get this language as their base language. In general this is best
    | to keep this in english (en)
    | Base language means the language that is used to translate into other languages
    | Example: if the base language is set to 'en', then all translations will be based on the english text.
    | Meaning your translators see the english text and need to translate it into french
    | if the base language is set to 'de' then all translations will be based on german.
    | Meaning your translators see the german text and need to translate it into french
    | STRONGLY RECOMMENDED to leave this in english, as most translators speak english
    |
    */
    'default_locale' => env('DEFAULT_LOCALE', 'en'),


    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    | The languages you want to use. Languages not listed here are simply ignored from pushing and pulling
    | ie ['en', 'nl', 'de']
    |
    */
    'locales' => ['en', 'nl', 'de'],

    /*
    |--------------------------------------------------------------------------
    | Readers
    |--------------------------------------------------------------------------
    | Define the source paths and type of readers you want to use
    |
    */
    'readers' => [
        [
            'path' => '/resources/lang',
            'class' => ReaderBlade::class,
        ],
        [
            'path' => '/resources/js/components/lang',
            'class' => ReaderVue::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Writers
    |--------------------------------------------------------------------------
    | Define the destination paths and the service to use to write
    |
    */
    'writers' => [
        [
            'path' => '/resources/lang_blade',
            'class' => GeneratorBlade::class,
        ],
        [
            'path' => '/resources/js/components/lang_vue',
            'class' => GeneratorVue::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Source files
    |--------------------------------------------------------------------------
    | Where your source files are located to search through to find extra undefined keys
    |
    */
    'source_code_scan_paths' => [
        'blade' => [
            '/resources/views/bedrock/admin'
            //'/app/',
            //'/config/',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Collect screenshots
    |--------------------------------------------------------------------------
    */
    'screenshot' => [

        /*
        |--------------------------------------------------------------------------
        | Collect screenshots
        |--------------------------------------------------------------------------
        | When to make the actual screenshots.
        | Always, never or only when there is a cookie present
        */
        'trigger' => 'ON_ENABLED_COOKIE', // ALWAYS | ON_ENABLED_COOKIE | NONE

        /*
        |--------------------------------------------------------------------------
        | Collect screenshot for which items
        |--------------------------------------------------------------------------
        | Collect the context for which items, All items, or only newly added items
        */
        'collect' => "ALL",  // ALL || NEW

        /*
        |--------------------------------------------------------------------------
        | Enabled
        |--------------------------------------------------------------------------
        | When the collect context is set to ON_ENABLED_COOKIE, this is the name of the cookie it checks.
        | If this cookie is set then the system collects the data, if cookie is not set the collection
        | is skipped
        | NOTE : This cookie needs to be unencrypted (place in your EncryptCookies.php except list)
        */
        "enabled_cookie" => "translationcaptain",


        /*
        |--------------------------------------------------------------------------
        | Collect storage
        |--------------------------------------------------------------------------
        | The name of the cookie to remember which keys are on this page for associating the screenshot to.
        | This will contain an array of keynames that will be associated to the captured screenshot
        | NOTE : This cookie needs to be unencrypted (place in your EncryptCookies.php except list)
        */
        "collect_cookie" => "translationcaptain_context",

        /*
        |--------------------------------------------------------------------------
        | Exclusions
        |--------------------------------------------------------------------------
        | List the URLS, Routes or Keys you want to exclude from screenshotting
        |
        */
        'exclude' => [

            "urls" => [
                "/home/text/exclued"
            ],

            "routes" => [
                "user.home1"
            ],

            "keys" => [
                "app.language"
            ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Log when missing keys found
    |--------------------------------------------------------------------------
    |
    */
    'log' => [
        'missing_keys' => env('TRANS_LOG_MISSING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue filename
    |--------------------------------------------------------------------------
    | the actual filename that is uses to store new found keys before
    | uploading them. This is just a temporary file
    |--------------------------------------------------------------------------
    |
    */
    'queue_filename' => 'translationcaption-queue.log',

    /*
    |--------------------------------------------------------------------------
    | Databinding (do not change)
    |--------------------------------------------------------------------------
    | Specify the start and end tokens of the databinding values.
    | TranslationCaptain needs to know how to recognize text in the translations that will be bound to a value later
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
];
