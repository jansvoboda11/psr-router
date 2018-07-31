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

        if ($failure === null || !$failure->isMethodAllowed("GET")) {
            return $handler->handle($request);
        }

        $getRequest = $request
            ->withoutAttribute(Failure::class)
            ->withMethod("GET");

        // cannot throw, GET method is allowed and will match
        $match = $this->router->match($getRequest);

        $response = $handler->handle($getRequest->withAttribute(Match::class, $match));

        $emptyStream = $this->streamFactory->createStream();

        return $response->withBody($emptyStream);
    }
}
