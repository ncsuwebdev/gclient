<?php

namespace NCSU\GClient;

use Illuminate\Support\ServiceProvider;

class GoogleClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/configs/google.php' => config_path('google.php')
        ],'config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       $this->mergeConfigFrom(__DIR__.'/configs/google.php', 'google');
       $this->app->bind('GClient', function($app) {
           dump($app['config']['google']);
        	return new Client($app['config']['google']);
        });
    }

    public function provides() {
	    return ['GClient'];
    }
}
