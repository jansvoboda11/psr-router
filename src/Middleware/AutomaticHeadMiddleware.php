<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
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
     * An empty response body.
     *
     * @var StreamInterface
     */
    private $emptyBody;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param StreamInterface $emptyBody
     */
    public function __construct(Router $router, StreamInterface $emptyBody)
    {
        $this->router = $router;
        $this->emptyBody = $emptyBody;
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

        if (!$failure || !$failure->isMethodFailure() || !in_array("GET", $failure->getAllowedMethods())) {
            return $handler->handle($request);
        }

        $getRequest = $request->withoutAttribute(Failure::class)->withMethod("GET");

        // cannot throw, because we pass the GET request and GET is also in allowed methods
        $match = $this->router->match($getRequest);

        $response = $handler->handle($getRequest->withAttribute(Match::class, $match));

        return $response->withBody($this->emptyBody);
    }
}
