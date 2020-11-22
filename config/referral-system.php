<?php

return [

    /*
    |--------------------------------------------------------------------------
    | query_parameter
    | The value that is visible in the url on where we can get the tracking info
    | ie example.com?via=xxxx or example.com?friend=xxxx
    |--------------------------------------------------------------------------
    |
    */

    'query_parameter' => 'via',


    /*
    |--------------------------------------------------------------------------
    | Cookie
    | Customize the lifetime how long a cookie is kept for awarding the referrer,
    | setting the name of the cookie as you like
    |--------------------------------------------------------------------------
    |
    |
    */
    'cookie' => [
        "name" => 'referral_cookie',
        "lifetime" => 60*24*7,
    ],

    /*
    |--------------------------------------------------------------------------
    | datetime_format
    | The format do display date time fields in.
    |--------------------------------------------------------------------------
    |
    */

    'datetime_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Models
    | User specific models
    |--------------------------------------------------------------------------
    |
    */
    'models' => [

        /*
        |--------------------------------------------------------------------------
        | Referrer Model
        | Only users already in the database can refer new people.
        |--------------------------------------------------------------------------
        |
        */
        "referrer" => [
            "class" => App\User::class,  // the actual class where your users are stored, usually the User Model

            /*
            |--------------------------------------------------------------------------
            | Public_id
            |--------------------------------------------------------------------------
            |
            | The name of the column of the user model to find the referrer.
            | This could be the database autoincrement 'id', but that would expose the internal id of the user in
            | all the links that they communicate to refer people
            | Better is to have a column in your user table where you store a unique(!) value (ie uuid) that the referrer
            | can use in their communication
            |
            */

            "public_id" => "xid",        // the column of the users table that is used as their tracking id

            /*
            |--------------------------------------------------------------------------
            | name
            |--------------------------------------------------------------------------
            |
            | The column name that shows the friendly name for the overview of who was being referred
            |
            */

            "name" => "name",            // the name of the user to display in the backend for your overview
        ]

    ],


    /*
    |--------------------------------------------------------------------------
    | Ui type : ignored when the view are published
    |--------------------------------------------------------------------------
    |
    | The type of ui you want to use if you are not publishing the resources
    | either "BLADE" only or "VUE" ui
    |
    */

    'ui_type' => 'VsUE', // BLADE | VUE

];
