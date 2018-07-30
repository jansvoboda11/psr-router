<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class TestCase extends \PHPUnit\Framework\TestCase
{
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
        return (new ServerRequest())->withMethod($method)->withUri(new Uri($uri));
    }

    /**
     * Creates new empty response.
     *
     * @param int $code
     * @param string $body
     * @return ResponseInterface
     */
    protected static function createResponse(int $code = 200, string $body = ""): ResponseInterface
    {
        return (new Response())->withStatus($code)->withBody(self::createStream($body));
    }

    /**
     * Creates new stream with the given string.
     *
     * @param string $string
     * @return StreamInterface
     */
    protected static function createStream(string $string = ""): StreamInterface
    {
        $stream = new Stream("php://temp", "wb+");

        $stream->write($string);
        $stream->rewind();

        return $stream;
    }
}
