<?php

declare(strict_types=1);

namespace Svoboda\Router\Semantics;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Types\Types;

/**
 * Validates the semantics of route path.
 */
class Validator
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
        $this->checkDuplicateAttributes($path);
        $this->checkAttributeTypes($path, $types);
    }

    /**
     * @param RoutePath $path
     * @throws InvalidRoute
     */
    private function checkDuplicateAttributes(RoutePath $path): void
    {
        $attributes = $path->getAttributes();

        $names = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $attributes);

        $nameCounts = array_count_values($names);

        foreach ($nameCounts as $name => $count) {
            if ($count > 1) {
                $definition = $path->getDefinition();

                throw InvalidRoute::ambiguousAttribute($definition, $name);
            }
        }
    }

    /**
     * @param RoutePath $path
     * @param Types $types
     * @throws InvalidRoute
     */
    private function checkAttributeTypes(RoutePath $path, Types $types): void
    {
        $attributes = $path->getAttributes();

        $typePatterns = $types->getPatterns();
        $implicitType = $types->getImplicit();

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $type = $attribute->getType() ?? $implicitType;

            if (!array_key_exists($type, $typePatterns)) {
                $definition = $path->getDefinition();

                throw InvalidRoute::unknownAttributeType($definition, $name, $type);
            }
        }
    }
}
