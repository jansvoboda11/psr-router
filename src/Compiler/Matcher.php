<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;

/**
 * Matches the incoming request.
 */
interface Matcher
{
    /**
     * Tries to match the incoming request and returns the result.
     *
     * @param ServerRequestInterface $request
     * @return Match
     * @throws Failure
     */
    public function match(ServerRequestInterface $request): Match;
}
