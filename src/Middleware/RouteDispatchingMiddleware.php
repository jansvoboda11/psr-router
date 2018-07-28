<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Match;

/**
 * Dispatches the request to the middleware of the matched route.
 * If the matching previously failed, it uses the default handler.
 */
class RouteDispatchingMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Match|null $match */
        $match = $request->getAttribute(Match::class);

        if (!$match) {
            return $handler->handle($request);
        }

        $middleware = $match->getMiddleware();
        $request = $match->getRequest();

        return $middleware->process($request, $handler);
    }
}
