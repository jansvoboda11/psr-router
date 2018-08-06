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
use Svoboda\Router\Route\Method;

/**
 * Automatically responds to HEAD requests.
 */
class AutomaticHeadMiddleware implements MiddlewareInterface
{
    /**
     * The stream factory.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Constructor.
     *
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== Method::HEAD) {
            return $handler->handle($request);
        }

        /** @var Failure|null $failure */
        $failure = $request->getAttribute(Failure::class);

        if ($failure === null) {
            return $handler->handle($request);
        }

        $getRoute = $failure->getUriRouteFor(Method::GET);

        if ($getRoute === null) {
            return $handler->handle($request);
        }

        $getRequest = $request
            ->withoutAttribute(Failure::class)
            ->withMethod(Method::GET);

        $getMatch = new Match($getRoute, $getRequest);

        $response = $handler->handle($getRequest->withAttribute(Match::class, $getMatch));

        $emptyStream = $this->streamFactory->createStream();

        return $response->withBody($emptyStream);
    }
}
