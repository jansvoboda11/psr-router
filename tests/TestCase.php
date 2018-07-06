<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }
}
