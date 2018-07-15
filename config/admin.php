<?php
return [
    /*
     * Version displayed in footer.
     */
    'version' => config('app.version', env('APP_VERSION')),

    /*
     * Laravel Admin Name.
     */
    'name' => config('app.name', 'Laravel') . ' Admin',

    /*
     * Laravel-admin html title.
     */
    'title' => config('app.name', 'Laravel') . ' Admin',

    /*
     * Route configuration.
     */
    'route' => [
        'prefix' => 'admin',
        'namespace' => 'App\\Admin\\Controllers',
        'middleware' => ['web', 'admin'],
    ],
    /*
     * Laravel-admin install directory.
     */
    'directory' => app_path('Http\\Controller\\Admin'),

    /*
     * Use `https`.
     */
    'secure' => true,

];
