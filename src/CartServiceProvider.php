<?php

namespace Mathewberry\Cart;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Mathewberry\Cart\Contracts\Cart;

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