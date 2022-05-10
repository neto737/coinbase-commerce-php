<?php

namespace CoinbaseCommerce\Exceptions;

class CoinbaseException extends \Exception
{
    public static function getClassName()
    {
        return static::class;
    }
}
