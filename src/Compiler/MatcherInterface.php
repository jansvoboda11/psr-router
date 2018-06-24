<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Match;

/**
 * Matches the incoming request and provides the result.
 */
interface MatcherInterface
{
    /**
     * Matches the incoming request.
     *
     * @param ServerRequestInterface $request
     * @return null|Match
     */
    public function match(ServerRequestInterface $request): ?Match;
}
