<?php

return [
    'default_date_format' => 'Y-m-d',
    'sentry_dsn' => env('SENTRY_DSN'),

    /*
    |--------------------------------------------------------------------------
    | Action enums
    |--------------------------------------------------------------------------
    | Enums implementing PActionType. PActionTypeCast walks this list to resolve
    | a stored string back to its case. Backing values must be unique across all
    | enums listed here.
    */
    'action_enums' => [],

];