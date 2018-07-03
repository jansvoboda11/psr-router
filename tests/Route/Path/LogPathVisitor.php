<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\PathVisitor;
use Svoboda\PsrRouter\Route\Path\RoutePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

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
    public function enterAttribute(AttributePath $path, &$data = null): void
    {
        $data[] = "Entering attribute " . $path->getName();
    }

    /**
     * @inheritdoc
     */
    public function leaveAttribute(AttributePath $path, &$data = null): void
    {
        $data[] = "Leaving attribute " . $path->getName();
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path, &$data = null): void
    {
        $data[] = "Entering optional";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path, &$data = null): void
    {
        $data[] = "Leaving optional";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path, &$data = null): void
    {
        $data[] = "Entering static " . $path->getStatic();
    }

    /**
     * @inheritdoc
     */
    public function leaveStatic(StaticPath $path, &$data = null): void
    {
        $data[] = "Leaving static " . $path->getStatic();
    }
}
