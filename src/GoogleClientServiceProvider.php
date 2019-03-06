<?php

namespace NCSU\GClient;

use Illuminate\Support\ServiceProvider;
use NCSU\GClient\Commands\AuthorizeGClient;

class GoogleClientServiceProvider extends ServiceProvider {
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        $this->publishes( [
            __DIR__ . '/configs/gclient.php' => config_path( 'gclient.php' )
        ], 'config' );

        if ( $this->app->runningInConsole() ) {
            $this->commands( [ AuthorizeGClient::class ] );
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom( __DIR__ . '/configs/gclient.php', 'gclient' );

        $this->app->bind( 'GClient', function ( $app ) {
            dump( $app['config']['gclient'] );

            return new Client( $app['config']['gclient'] );
        } );
    }

    public function provides() {
        return [ 'GClient' ];
    }
}
