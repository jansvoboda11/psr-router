<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route\Path;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use SvobodaTest\Router\TestCase;

class PartsVisitorTest extends TestCase
{
    public function test_it_visits_path_parts_in_correct_order()
    {
        $path = new StaticPath(
            "/users",
            new OptionalPath(
                new StaticPath(
                    "/",
                    new AttributePath(
                        "id",
                        "num"
                    )
                )
            )
        );

        $visitor = new LoggingPathVisitor();

        $log = $visitor->log($path);

        self::assertEquals([
            "Entering static /users",
            "Entering optional",
            "Entering static /",
            "Entering attribute id",
            "Leaving attribute id",
            "Leaving static /",
            "Leaving optional",
            "Leaving static /users",
        ], $log);
    }
}
