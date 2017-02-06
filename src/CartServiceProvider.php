<?php

namespace Mathewberry\Cart;

use Illuminate\Http\Request;
use Mathewberry\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mathewberry.cart', function (Request $request) {
           $cart = new Cart($request);
           return $cart;
        });

        $this->app->alias('mathewberry.cart', Cart::class);
    }

    /**
     * Boot using Laravel setup.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function bootUsingLaravel($path)
    {
        $path = realpath(__DIR__.'/../resources');

        $this->mergeConfigFrom("{path}/config/config.php", 'mathewberry.cart');

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