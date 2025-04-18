<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'api', // Using 'api' guard by default
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | The "api" guard is the one we will use for our API authentication, 
    | which is configured to use Sanctum as the driver.
    |
    | Laravel also supports session-based authentication through the "web" guard.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'sanctum', // Use Sanctum for API authentication
            'provider' => 'users', // Using the 'users' provider for the API guard
            'hash' => false, // Typically false for API tokens since they're hashed differently
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Here we define how to retrieve the users from your database or other
    | storage mechanisms. The default provider is the 'users' provider
    | which uses Eloquent to get user data.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent', // Eloquent ORM to fetch users from the database
            'model' => App\Models\Tenant\User::class, // The User model to use
        ],

        // You can also add other user providers here if needed (e.g., if you have more user tables)
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Configuration
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application. This option will be
    | used to configure the password reset settings for your users.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60, // Expiry time in minutes
            'throttle' => 60, // Throttle time in seconds to prevent spamming requests
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800, // Time before a user must re-enter their password (3 hours)

];
