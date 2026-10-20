<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model discovery
    |--------------------------------------------------------------------------
    |
    | Discovery describes model metadata only. Models must be explicitly
    | allowlisted here before a future retrieval feature may use them.
    |
    */
    'model_directories' => [
        [
            'path' => 'app/Models',
            'namespace' => 'App\\Models',
        ],
    ],

    'enabled_models' => [],

    'excluded_models' => [],
];
