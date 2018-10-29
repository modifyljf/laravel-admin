<?php

namespace Guesl\Admin\Providers;

use Guesl\Admin\Notifications\Channels\TwilioSmsChannel;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

class TwilioSmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TwilioSmsChannel::class, function ($app) {
            return new TwilioSmsChannel(
                new Client($app['config']['services.twilio.key'], $app['config']['services.twilio.secret']),
                $app['config']['services.twilio.from']
            );
        });
    }
}
