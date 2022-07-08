<?php

namespace Modifyljf\Admin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class BeanServiceProvider
 * @package Modifyljf\Admin\Providers
 */
class BeanServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $beans = config('beans');
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
