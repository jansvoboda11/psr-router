<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Router;

/**
 * Automatically responds to HEAD requests.
 */
class AutomaticHeadMiddleware implements MiddlewareInterface
{
    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * The stream factory.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(Router $router, StreamFactoryInterface $streamFactory)
    {
        $this->router = $router;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== "HEAD") {
            return $handler->handle($request);
        }

        /** @var Failure|null $failure */
        $failure = $request->getAttribute(Failure::class);

        if ($failure === null || !$this->isGetRouteReachable($failure)) {
            return $handler->handle($request);
        }

        $getRequest = $request
            ->withoutAttribute(Failure::class)
            ->withMethod("GET");

        // cannot throw, GET route is reachable
        $match = $this->router->match($getRequest);

        $response = $handler->handle($getRequest->withAttribute(Match::class, $match));

        $emptyStream = $this->streamFactory->createStream();

        return $response->withBody($emptyStream);
    }

    /**
     * Determine if the GET route is reachable.
     *
     * @param Failure $failure
     * @return bool
     */
    private function isGetRouteReachable(Failure $failure): bool
    {
        return $failure->isMethodFailure()
            && in_array("GET", $failure->getAllowedMethods());
    }
}
