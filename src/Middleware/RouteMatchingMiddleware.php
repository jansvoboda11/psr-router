<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Match;
use Svoboda\Router\Failure;
use Svoboda\Router\Router;

/**
 * Tries to match the incoming request using the provided router.
 * Adds the match or failure as request attribute.
 */
class RouteMatchingMiddleware implements MiddlewareInterface
{
    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $match = $this->router->match($request);
        } catch (Failure $failure) {
            return $handler->handle($request->withAttribute(Failure::class, $failure));
        }

        return $handler->handle($request->withAttribute(Match::class, $match));
    }
}
