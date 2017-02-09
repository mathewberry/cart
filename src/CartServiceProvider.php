<?php

namespace Mathewberry\Cart;

use Illuminate\Support\ServiceProvider;
use Mathewberry\Cart\Contracts\Cart;

class CartServiceProvider extends ServiceProvider
{
    /**
     * register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mathewberry.cart', function () {
            return new Cart($this->app->make('request'));
        });

        $this->bootUsingLaravel();
    }

    /**
     * Boot using Laravel setup.
     *
     * @return void
     */
    protected function bootUsingLaravel()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->mergeConfigFrom("{$path}/config/config.php", 'mathewberry.cart');

        $this->publishes([
            "{$path}/config/config.php" => config_path('mathewberry/cart.php')
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mathewberry.cart'];
    }
}