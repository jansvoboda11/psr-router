<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Semantics;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Path\RoutePath;

/**
 * Validates the semantics of route path.
 */
class Validator
{
    /**
     * Checks the semantic validity of given route path.
     *
     * @param RoutePath $path
     * @throws InvalidRoute
     */
    public function validate(RoutePath $path): void
    {
        $attributes = $path->getAttributes();

        $names = array_column($attributes, "name");

        $counts = array_count_values($names);

        $duplicates = array_keys(array_filter($counts, function ($count) {
            return $count > 1;
        }));

        if (!empty($duplicates)) {
            $definition = $path->getDefinition();

            throw InvalidRoute::ambiguousAttribute($definition, $duplicates);
        }
    }
}
