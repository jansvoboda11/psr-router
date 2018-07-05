<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Semantics;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use Svoboda\PsrRouter\Semantics\Validator;
use SvobodaTest\PsrRouter\TestCase;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator();
    }
    
    /**
     * @doesNotPerformAssertions
     */
    public function test_path_without_attributes()
    {
        $path = new StaticPath("");

        $this->validator->validate($path);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_path_with_one_required_attribute()
    {
        $path = new AttributePath("name", null);

        $this->validator->validate($path);
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

        $this->validator->validate($path);
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

        $this->validator->validate($path);
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

        $this->validator->validate($path);
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

        $this->validator->validate($path);
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

        $this->validator->validate($path);
    }
}
