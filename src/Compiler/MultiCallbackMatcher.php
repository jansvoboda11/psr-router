<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;

class MultiCallbackMatcher implements Matcher
{
    /**
     * Array of callback - route pairs.
     *
     * @var array
     */
    private $records;

    /**
     * Constructor.
     *
     * @param array $records
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): Match
    {
        // todo: implement

        throw new Failure([], $request);
    }
}
