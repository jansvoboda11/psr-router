<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

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

        $visitor = new LogPathVisitor();

        $path->accept($visitor);

        self::assertEquals([
            "Entering static /users",
            "Entering optional",
            "Entering static /",
            "Entering attribute id",
            "Leaving attribute id",
            "Leaving static /",
            "Leaving optional",
            "Leaving static /users",
        ], $visitor->getLog());
    }
}
