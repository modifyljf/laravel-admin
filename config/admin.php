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
        'prefix' => '',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['web'],
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
