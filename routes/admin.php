<?php

use Illuminate\Routing\Route;

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function () {
    Route::get('/', 'HomeController@index');
});
