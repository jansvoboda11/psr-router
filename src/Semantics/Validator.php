<?php

declare(strict_types=1);

namespace Svoboda\Router\Semantics;

use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Types\Types;

/**
 * Validates the semantics of route path.
 */
class Validator extends PathVisitor
{
    /**
     * Checks the semantic validity of given route path.
     *
     * @param RoutePath $path
     * @param Types $types
     * @throws InvalidRoute
     */
    public function validate(RoutePath $path, Types $types): void
    {
        $data = [
            "definition" => $path->getDefinition(),
            "implicitType" => $types->getImplicit(),
            "typePatterns" => $types->getPatterns(),
            "attributes" => [],
        ];

        $path->accept($this, $data);
    }

    /**
     * Checks the uniqueness of the attribute and validity of its type.
     *
     * @param AttributePath $path
     * @param mixed $data
     * @throws InvalidRoute
     */
    public function enterAttribute(AttributePath $path, &$data): void
    {
        $implicitType = $data["implicitType"];
        $typePatterns = $data["typePatterns"];
        $attributes = &$data["attributes"];

        $definition = $data["definition"];

        $name = $path->getName();

        if (in_array($name, $attributes)) {
            throw InvalidRoute::ambiguousAttribute($definition, $name);
        }

        $attributes[] = $name;

        $type = $path->getType() ?? $implicitType;

        if (!array_key_exists($type, $typePatterns)) {
            throw InvalidRoute::unknownAttributeType($definition, $name, $type);
        }
    }
}
