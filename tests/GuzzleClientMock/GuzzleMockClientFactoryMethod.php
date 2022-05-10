<?php

namespace CoinbaseCommerce\Tests\GuzzleClientMock;

class GuzzleMockClientFactoryMethod
{
    public static function create()
    {
        if (\class_exists('GuzzleHttp\Handler\MockHandler')) {
            return new NewGuzzleHelperHelper();
        } else {
            throw new \Exception('Unsupported Guzzle version.');
        }
    }
}
