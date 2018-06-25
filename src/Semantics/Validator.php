<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Semantics;

use Svoboda\PsrRouter\InvalidRoute;
use Svoboda\PsrRouter\Parser\Parts\RoutePart;

/**
 * Validates the semantics of route definitions.
 */
class Validator
{
    /**
     * Check the semantic validity of given route.
     *
     * @param RoutePart $ast
     * @throws InvalidRoute
     */
    public function validate(RoutePart $ast): void
    {
        $attributes = $ast->getAttributes();

        $names = array_column($attributes, "name");

        $counts = array_count_values($names);

        $duplicates = array_keys(array_filter($counts, function ($count) {
            return $count > 1;
        }));

        if (!empty($duplicates)) {
            $path = $ast->getDefinition();

            throw InvalidRoute::ambiguousAttribute($path, $duplicates);
        }
    }
}
