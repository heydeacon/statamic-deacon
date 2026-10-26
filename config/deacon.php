<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Key
    |--------------------------------------------------------------------------
    |
    | Your Deacon key: the data-key value in the snippet on Install, the
    | button at the top of the Deacon dashboard. It starts with pk_. With no
    | key the tag prints nothing and logs a warning.
    |
    */

    'key' => env('DEACON_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Context
    |--------------------------------------------------------------------------
    |
    | Details Deacon takes into account when it answers, the same on every
    | page, such as ['product' => 'api', 'docs_version' => '3.2']. Up to 12
    | keys, each value a string, number or boolean. Anyone can read them in
    | the page's HTML, so never put a secret here.
    |
    */

    'context' => null,

    /*
    |--------------------------------------------------------------------------
    | Page views
    |--------------------------------------------------------------------------
    |
    | The same script counts visits for Deacon's website analytics. Set this
    | to false to send no page views or JavaScript errors from your site.
    | Chat still works.
    |
    */

    'page_views' => true,

];
