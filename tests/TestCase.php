<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }
}
