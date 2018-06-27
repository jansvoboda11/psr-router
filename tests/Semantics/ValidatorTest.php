<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Semantics;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\EmptyPath;
use Svoboda\PsrRouter\Route\Path\MainPath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use Svoboda\PsrRouter\Semantics\Validator;
use SvobodaTest\PsrRouter\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function test_path_without_attributes()
    {
        $path = new MainPath(
            new StaticPath(""),
            [],
            new EmptyPath()
        );

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_required_attribute()
    {
        $path = new MainPath(
            new StaticPath(""),
            [
                new AttributePath("name")
            ],
            new EmptyPath()
        );

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_optional_attribute()
    {
        $path = new MainPath(
            new StaticPath(""),
            [],
            new OptionalPath(
                new MainPath(
                    new StaticPath(""),
                    [
                        new AttributePath("name"),
                    ],
                    new EmptyPath()
                )
            )
        );

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_two_attributes()
    {
        $path = new MainPath(
            new StaticPath(""),
            [
                new AttributePath("id"),
                new AttributePath("name"),
            ],
            new EmptyPath()
        );

        (new Validator())->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_required_and_optional_attribute()
    {
        $path = new MainPath(
            new StaticPath(""),
            [
                new AttributePath("id"),
            ],
            new OptionalPath(
                new MainPath(
                    new StaticPath(""),
                    [
                        new AttributePath("name"),
                    ],
                    new EmptyPath()
                )
            )
        );

        (new Validator())->validate($path);
    }

    public function test_path_with_two_required_attributes_of_same_name()
    {
        $path = new MainPath(
            new StaticPath("/users/"),
            [
                new AttributePath("id"),
                new AttributePath("id"),
            ],
            new EmptyPath()
        );

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}{id}
MESSAGE
        );

        (new Validator())->validate($path);
    }

    public function test_path_with_required_and_optional_attribute_of_same_name()
    {
        $path = new MainPath(
            new StaticPath("/users/"),
            [
                new AttributePath("id"),
            ],
            new OptionalPath(
                new MainPath(
                    new StaticPath(""),
                    [
                        new AttributePath("id"),
                    ],
                    new EmptyPath()
                )
            )
        );

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}[{id}]
MESSAGE
        );

        (new Validator())->validate($path);
    }
}
