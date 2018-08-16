<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\RouteCollection;

class MultiCallbackCompiler implements Compiler
{
    /**
     * The callback factory.
     *
     * @var CallbackFactory
     */
    private $callbackFactory;

    /**
     * Constructor.
     *
     * @param CallbackFactory $callbackFactory
     */
    public function __construct(CallbackFactory $callbackFactory)
    {
        $this->callbackFactory = $callbackFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        $records = [];

        foreach ($routes->all() as $route) {
            $path = $route->getPath();

            $pathCallback = $this->callbackFactory->create($path);

            $records[] = [$pathCallback, $route];
        }

        return new MultiCallbackMatcher($records);
    }
}
