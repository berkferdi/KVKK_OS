<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup disk & path
    |--------------------------------------------------------------------------
    */
    'disk' => env('BACKUP_DISK', 'local'),
    'directory' => env('BACKUP_DIRECTORY', 'backups'),

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | Keep the newest N successful archives; older completed ones are deleted.
    |
    */
    'keep' => (int) env('BACKUP_KEEP', 14),

    /*
    |--------------------------------------------------------------------------
    | Optional: include selected storage files in the ZIP
    |--------------------------------------------------------------------------
    */
    'include_storage' => (bool) env('BACKUP_INCLUDE_STORAGE', false),
];
