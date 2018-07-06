<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Route\Path;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

/**
 * Visits path parts and logs the order.
 */
class LogPathVisitor extends PathVisitor
{
    /**
     * Visits the route path and returns a log of every encountered node.
     *
     * @param RoutePath $path
     * @return array
     */
    public function visit(RoutePath $path)
    {
        $logs = [];

        $path->accept($this, $logs);

        return $logs;
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path, &$data): void
    {
        $data[] = "Entering attribute " . $path->getName();
    }

    /**
     * @inheritdoc
     */
    public function leaveAttribute(AttributePath $path, &$data): void
    {
        $data[] = "Leaving attribute " . $path->getName();
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path, &$data): void
    {
        $data[] = "Entering optional";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path, &$data): void
    {
        $data[] = "Leaving optional";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path, &$data): void
    {
        $data[] = "Entering static " . $path->getStatic();
    }

    /**
     * @inheritdoc
     */
    public function leaveStatic(StaticPath $path, &$data): void
    {
        $data[] = "Leaving static " . $path->getStatic();
    }
}
