<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Route\Path;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

class LoggingPathVisitor extends PathVisitor
{
    private $logs = [];

    public function __construct()
    {
        $this->logs = [];
    }

    public function log(RoutePath $path): array
    {
        $path->accept($this);

        return $this->logs;
    }

    public function enterAttribute(AttributePath $path): void
    {
        $this->logs[] = "Entering attribute " . $path->getName();
    }

    public function leaveAttribute(AttributePath $path): void
    {
        $this->logs[] = "Leaving attribute " . $path->getName();
    }

    public function enterOptional(OptionalPath $path): void
    {
        $this->logs[] = "Entering optional";
    }

    public function leaveOptional(OptionalPath $path): void
    {
        $this->logs[] = "Leaving optional";
    }

    public function enterStatic(StaticPath $path): void
    {
        $this->logs[] = "Entering static " . $path->getStatic();
    }

    public function leaveStatic(StaticPath $path): void
    {
        $this->logs[] = "Leaving static " . $path->getStatic();
    }
}
