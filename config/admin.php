<?php

return [
    /*
     * Version displayed in footer.
     */
    'version' => env('APP_VERSION', 'v1.0.0'),

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
        'prefix' => '',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['web', 'auth'],
    ],

    /*
     * Laravel-admin install directory.
     */
    'directory' => app_path('Http/Controllers/Admin'),

    /*
     * Use `https`.
     */
    'secure' => true,

];
