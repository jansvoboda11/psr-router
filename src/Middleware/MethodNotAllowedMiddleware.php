<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Route\Method;

/**
 * Automatically creates 405 responses (method not allowed).
 */
class MethodNotAllowedMiddleware implements MiddlewareInterface
{
    /**
     * The response interface.
     *
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Method::isAlwaysAllowed($request->getMethod())) {
            return $handler->handle($request);
        }

        /** @var Failure|null $failure */
        $failure = $request->getAttribute(Failure::class);

        if (!$failure || !$failure->isMethodFailure()) {
            return $handler->handle($request);
        }

        $allow = implode(", ", $failure->getAllowedMethods());

        return $this->responseFactory
            ->createResponse(405, "Method Not Allowed")
            ->withHeader("Allow", $allow);
    }
}
