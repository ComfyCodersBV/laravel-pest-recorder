<?php

declare(strict_types=1);

// config for TranquilTools/PestRecorder
return [

    /*
    |--------------------------------------------------------------------------
    | Default test output path
    |--------------------------------------------------------------------------
    |
    | Base directory where recorded Pest browser tests will be written.
    | The path is relative to the Laravel base path.
    |
    */
    'default_test_path' => 'tests/Browser',

    /*
    |--------------------------------------------------------------------------
    | Selector priority
    |--------------------------------------------------------------------------
    |
    | Ordered list of selector strategies used when generating assertions.
    |
    */
    'selector_priority' => [
        'data-test',
        'data-testid',
        'test-id',
        'role',
        'text',
        'css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver-specific configuration
    |--------------------------------------------------------------------------
    |
    | Internal driver options. These are NOT part of the public API and may
    | change without notice.
    |
    */
    'drivers' => [

        /*
        |--------------------------------------------------------------------------
        | Playwright driver
        |--------------------------------------------------------------------------
        */
        'playwright' => [

            /*
            |--------------------------------------------------------------
            | Test ID attribute
            |--------------------------------------------------------------
            |
            | Attribute name used by Playwright's getByTestId() selector.
            |
            */
            'test_id_attribute' => 'id',

            /*
            |--------------------------------------------------------------
            | Ignore HTTPS errors
            |--------------------------------------------------------------
            |
            | Whether Playwright should ignore invalid HTTPS certificates.
            | Useful for local development environments.
            |
            */
            'ignore_https_errors' => true,
        ],
    ],
];
