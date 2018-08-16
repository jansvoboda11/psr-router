<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Route\Route;

class TestCase extends \PHPUnit\Framework\TestCase
{
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

    /**
     * Creates new request that contains a match with the given route.
     *
     * @param ServerRequestInterface $request
     * @param Route $route
     * @return ServerRequestInterface
     */
    protected static function requestWithMatch(ServerRequestInterface $request, Route $route): ServerRequestInterface
    {
        $match = new Match($route, $request);

        return $request->withAttribute(Match::class, $match);
    }

    /**
     * Creates new request that contains a failure with the given URI routes.
     *
     * @param ServerRequestInterface $request
     * @param Route[] $uriRoutes
     * @return ServerRequestInterface
     */
    protected static function requestWithFailure(ServerRequestInterface $request, array $uriRoutes): ServerRequestInterface
    {
        $failure = new Failure($uriRoutes, $request);

        return $request->withAttribute(Failure::class, $failure);
    }
}
