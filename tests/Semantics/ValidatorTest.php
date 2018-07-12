<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Semantics;

use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Semantics\Validator;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    private $validator;

    /** @var Types */
    private $types;

    protected function setUp()
    {
        $this->validator = new Validator();

        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_without_attributes()
    {
        $path = new StaticPath("");

        $this->validator->validate($path, $this->types);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_required_attribute()
    {
        $path = new AttributePath("name", null);

        $this->validator->validate($path, $this->types);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_optional_attribute()
    {
        $path = new OptionalPath(
            new AttributePath(
                "name",
                null
            )
        );

        $this->validator->validate($path, $this->types);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_two_attributes()
    {
        $path = new AttributePath(
            "id",
            null,
            new AttributePath(
                "name",
                null
            )
        );

        $this->validator->validate($path, $this->types);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_required_and_optional_attribute()
    {
        $path = new AttributePath(
            "id",
            null,
            new OptionalPath(
                new AttributePath(
                    "name",
                    null
                )
            )
        );

        $this->validator->validate($path, $this->types);
    }

    public function test_path_with_two_required_attributes_of_same_name()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                null,
                new AttributePath(
                    "id",
                    null
                )
            )
        );

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}{id}
MESSAGE
        );

        $this->validator->validate($path, $this->types);
    }

    public function test_path_with_required_and_optional_attribute_of_same_name()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                null,
                new OptionalPath(
                    new AttributePath(
                        "id",
                        null
                    )
                )
            )
        );

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}[{id}]
MESSAGE
        );

        $this->validator->validate($path, $this->types);
    }
}
