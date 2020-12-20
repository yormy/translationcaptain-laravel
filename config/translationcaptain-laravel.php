<?php
//    'context_url' => "https://backend.bedrock.local/api/v1/multilingual/labels/context/opencontext",
//    'context_url' => "https://backend.bedrock.local/api/v1/multilingual/labels/context/opencontext",
//


/*
|--------------------------------------------------------------------------
| Pre / Postfix
|--------------------------------------------------------------------------
|
| This is the initial text that will be prepended or appended to the text in the database
| when the newly translated item is created from the key : the part __('key')
| Either one of these 2 is necessary to indicate that the itme is already
| added to the database. It is also a visual marker that this item is
| not yet translated
|
*/
//    'label_missing_prefix' =>'#',
//    'label_missing_postfix' =>'?',
return [
    /*
    |--------------------------------------------------------------------------
    | Enabled or not
    |--------------------------------------------------------------------------
    | Enable or disable the entire translationcaptain functionality
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
    | The id of the project on translationcaptain for which these keys are used
    */
    'projectId' => 'C0QEÄhLYSqiNCePeORF9gw',

/*
|--------------------------------------------------------------------------
| Default Locale
|--------------------------------------------------------------------------
| This is de default base locale. New translation lines stored in the database will
| automatically get this language as their base language. In general this is best
| to keep this in english (en)
| Base language means the language that is used to translate into other languages
|
*/
    'default_locale' => env('DEFAULT_LOCALE', 'nl'),

    /*
    |--------------------------------------------------------------------------
    | screenshot_collect_trigger
    |--------------------------------------------------------------------------
    | When to make the actual screenshots.
    | Always, never or only when there is a cookie present
    */
    'screenshot_collect_trigger' => 'ON_ENABLED_COOKIE', // ALWAYS | ON_ENABLED_COOKIE | NONE

    /*
    |--------------------------------------------------------------------------
    | Collect screenshot for which items
    |--------------------------------------------------------------------------
    | Collect the context for which items, All items, or only newly added items
    */
    'screenshot_collect_for' => "ALL",  // ALL || NEW

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

    /// CLEANUP BELOW

    /*
    |--------------------------------------------------------------------------
    | Cookie names
    |--------------------------------------------------------------------------
    | These cookies needs to bo non secure and non encrypted, as we need to read this from javascript
    | to make the actual screenshot. The cookie is only used in the frontend, so no security issues
    | NOTE : place in your EncryptCookies.php except list
    */
    "cookie" => [
//    locale

        /*
        |--------------------------------------------------------------------------
        | Collect storage
        |--------------------------------------------------------------------------
        | The name of the cookie to remember which keys are on this page for associating the screenshot to.
        | This will contain an array of keynames that will be associated to the captured screenshot
        | NOTE : This cookie needs to be unencrypted (place in your EncryptCookies.php except list)
        */
        "collect" => "translationcaptain_context",

        /*
        |--------------------------------------------------------------------------
        | Enabled
        |--------------------------------------------------------------------------
        | When the collect context is set to ON_ENABLED_COOKIE, this is the name of the cookie it checks.
        | If this cookie is set then the system collects the data, if cookie is not set the collection
        | is skipped
        | NOTE : This cookie needs to be unencrypted (place in your EncryptCookies.php except list)
        */
        "screenshot_enabled" => "translationcaptain"
    ],





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
        'on_missing_key' => false
    ],



    'log_missing_keys' => env('TRANS_LOG_MISSING', true),

    'queue_filename' => 'translationcaption-queue.log',

    'url' => 'localhost/api/v1/translationcaptain', // 'https://backend.bedrock.local/

    'context_url' => "https://backend.bedrock.local/api/v1/multilingual/labels/context/opencontext",


];
