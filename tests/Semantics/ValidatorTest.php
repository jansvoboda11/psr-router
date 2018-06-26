<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Semantics;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Semantics\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function test_path_without_attributes()
    {
        $path = (new Parser())->parse("/users");

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_required_attribute()
    {
        $path = (new Parser())->parse("/users/{name}");

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_optional_attribute()
    {
        $path = (new Parser())->parse("/users[/{name}]");

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_two_attributes()
    {
        $path = (new Parser())->parse("/users/{id}/{name}");

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_required_and_optional_attribute()
    {
        $path = (new Parser())->parse("/users/{id}[/{name}]");

        (new Validator())->validate($path);
    }

    public function test_path_with_two_required_attributes_of_same_name()
    {
        $path = (new Parser())->parse("/users/{id}/{id}");

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}/{id}
MESSAGE
        );

        (new Validator())->validate($path);
    }

    public function test_path_with_required_and_optional_attribute_of_same_name()
    {
        $path = (new Parser())->parse("/users/{id}[/{id}]");

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}[/{id}]
MESSAGE
        );

        (new Validator())->validate($path);
    }
}
