<?php

namespace CoinbaseCommerce\Tests\GuzzleClientMock;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

class NewGuzzleHelperHelper extends GuzzleHelperAbstract
{
    protected $client;

    protected $mock;

    protected $container = [];

    public function __construct()
    {
        $this->mock = new MockHandler();
        $history = Middleware::history($this->container);
        $handler = HandlerStack::create($this->mock);
        $handler->push($history);

        $this->client = new Client(['handler' => $handler]);
    }

    public function shiftTransactionRequest()
    {
        $request = \array_shift($this->container)['request'];

        return [
            'params' => $request->getUri()->getQuery(),
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
        ];
    }

    public function appendRequest($statusCode, $body, $headers = [])
    {
        $this->mock->append(new Response($statusCode, $headers, \json_encode($body)));
    }
}
