<?php

namespace Mathewberry\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'mathewberry.cart';
    }
}