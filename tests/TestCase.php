<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Mockery;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use ThrowableExpectations;

    protected function setUp()
    {
        $this->setUpThrowableExpectations();
    }

    protected function runTest()
    {
        $this->handleThrowableExpectations(function () {
            parent::runTest();
        });
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * Creates new server request with given method and URI.
     *
     * @param string $method
     * @param string $uri
     * @return ServerRequestInterface
     */
    protected static function createRequest(string $method, string $uri): ServerRequestInterface
    {
        return (new Psr17Factory())->createServerRequest($method, $uri);
    }

    /**
     * Creates new empty response.
     *
     * @param int $code
     * @param string $reasonPhrase
     * @param string $body
     * @return ResponseInterface
     */
    protected static function createResponse(int $code = 200, string $reasonPhrase = "", string $body = ""): ResponseInterface
    {
        $body = self::createStream($body);

        return (new Psr17Factory())->createResponse($code, $reasonPhrase)->withBody($body);
    }

    /**
     * Creates new stream with the given string.
     *
     * @param string $string
     * @return StreamInterface
     */
    protected static function createStream(string $string = ""): StreamInterface
    {
        return (new Psr17Factory())->createStream($string);
    }
}
