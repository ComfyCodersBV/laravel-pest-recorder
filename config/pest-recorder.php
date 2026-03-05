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
    | Acting-as storage path
    |--------------------------------------------------------------------------
    |
    | Directory where named auth state files (JSON) are stored when using
    | the --acting-as flag. The path is relative to the Laravel storage path.
    |
    */
    'browser_chrome_width_margin' => 30,
    'browser_chrome_height_margin' => 70,

    'acting_as_storage_path' => 'app/tmp/auth',

    /*
    |--------------------------------------------------------------------------
    | Acting-as login path
    |--------------------------------------------------------------------------
    |
    | The URI path opened in the browser when recording a login sequence
    | for the first time (i.e. when no auth state file exists yet).
    |
    */
    'acting_as_login_path' => '/login',

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
