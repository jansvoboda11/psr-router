<?php

declare(strict_types=1);

namespace Svoboda\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;

/**
 * Automatically responds to OPTIONS requests.
 */
class AutomaticOptionsMiddleware implements MiddlewareInterface
{
    /**
     * An empty HTTP response.
     *
     * @var ResponseInterface
     */
    private $emptyResponse;

    /**
     * Constructor.
     *
     * @param ResponseInterface $emptyResponse
     */
    public function __construct(ResponseInterface $emptyResponse)
    {
        $this->emptyResponse = $emptyResponse;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== "OPTIONS") {
            return $handler->handle($request);
        }

        /** @var ?Failure $failure */
        $failure = $request->getAttribute(Failure::class);

        if (!$failure || !$failure->isMethodFailure()) {
            return $handler->handle($request);
        }

        $allow = implode(", ", $failure->getAllowedMethods());

        return $this->emptyResponse->withHeader("Options", $allow);
    }
}
