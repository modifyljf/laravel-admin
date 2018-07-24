<?php

namespace Guesl\Admin\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        'Guesl\Admin\Console\Commands\InstallAdminCommand',
        'Guesl\Admin\Console\Commands\UninstallAdminCommand',
        'Guesl\Admin\Console\Commands\AuthMakeCommand',
        'Guesl\Admin\Console\Commands\AdminMakeCommand',
        'Guesl\Admin\Console\Commands\GenerateCommand',
        'Guesl\Admin\Console\Commands\ControllerMakeCommand',
        'Guesl\Admin\Console\Commands\ViewMakeCommand',
        'Guesl\Admin\Console\Commands\JsMakeCommand',
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouteMiddleware();
        $this->registerBeansContainer();
        $this->commands($this->commands);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAdminRoutes();
    }

    /**
     * Define the "admin routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        if (file_exists(base_path('routes/admin.php'))) {
            Route::middleware(config('admin.route.middleware'))
                ->namespace(config('admin.route.namespace'))
                ->prefix(config('admin.route.prefix'))
                ->group(base_path('routes/admin.php'));
        }
    }

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Register the route middleware.
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Register beans.
     */
    protected function registerBeansContainer()
    {
        $beans = config('beans');

        if ($beans) {
            foreach ($beans as $i => $impl) {
                if ($impl['singleton']) {
                    $this->app->singleton($i, function () use ($impl) {
                        return $this->app->make($impl['class']);
                    }, empty($impl['shared']) ? null : $impl['shared']);
                } else {
                    $this->app->bind($i, function () use ($impl) {
                        return $this->app->make($impl['class']);
                    }, empty($impl['shared']) ? null : $impl['shared']);
                }
            }
        }
    }
}
