<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Types;

/**
 * Builds the URI based on the route path and provided attributes.
 */
class UriBuilder extends PathVisitor
{
    /**
     * Builds the URI for route with given attributes.
     *
     * @param RoutePath $path
     * @param Types $types
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     */
    public function buildUri(RoutePath $path, Types $types, array $attributes = []): string
    {
        $data = [
            "uri" => "",
            "done" => false,
            "attributes" => $attributes,
            "implicitType" => $types->getImplicit(),
            "typePatterns" => $types->getPatterns(),
        ];

        $path->accept($this, $data);

        return $data["uri"];
    }

    /**
     * Adds the attribute value to the URI.
     *
     * @param AttributePath $path
     * @param mixed $data
     * @throws InvalidAttribute
     */
    public function enterAttribute(AttributePath $path, &$data): void
    {
        if ($data["done"]) {
            return;
        }

        $implicitType = $data["implicitType"];
        $typePatterns = $data["typePatterns"];

        $name = $path->getName();
        $value = $this->getValue($path, $data["attributes"]);
        $pattern = $this->getTypePattern($path, $implicitType, $typePatterns);

        $this->validateValue($name, $value, $pattern);

        $data["uri"] .= $value;
    }

    /**
     * Determines if the optional path should be present in the URI.
     *
     * @param OptionalPath $path
     * @param mixed $data
     */
    public function enterOptional(OptionalPath $path, &$data): void
    {
        if (!$this->optionalAttributesProvided($path, $data["attributes"])) {
            $data["done"] = true;
        }
    }

    /**
     * Adds the static part of the path to the URI.
     *
     * @param StaticPath $path
     * @param mixed $data
     */
    public function enterStatic(StaticPath $path, &$data): void
    {
        if ($data["done"]) {
            return;
        }

        $data["uri"] .= $path->getStatic();
    }

    /**
     * Returns the value of the attribute.
     *
     * @param AttributePath $path
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     */
    private function getValue(AttributePath $path, array $attributes): string
    {
        $name = $path->getName();

        if (!array_key_exists($name, $attributes)) {
            throw InvalidAttribute::missing($name);
        }

        return (string)$attributes[$name];
    }

    /**
     * Returns the regular expression for the attribute type.
     *
     * @param AttributePath $path
     * @param string $implicitType
     * @param array $typePatterns
     * @return string
     * @throws InvalidAttribute
     */
    private function getTypePattern(AttributePath $path, string $implicitType, array $typePatterns): string
    {
        $type = $path->getType() ?? $implicitType;

        if (!array_key_exists($type, $typePatterns)) {
            $name = $path->getName();

            throw InvalidAttribute::unknownType($name, $type);
        }

        return $typePatterns[$type];
    }

    /**
     * Checks whether the value matches the regular expression.
     *
     * @param string $name
     * @param string $value
     * @param string $pattern
     * @throws InvalidAttribute
     */
    private function validateValue(string $name, string $value, string $pattern): void
    {
        if (!preg_match("#^$pattern$#", $value)) {
            throw InvalidAttribute::badFormat($name, $value, $pattern);
        }
    }

    /**
     * Determines if there were any optional attributes provided by the user.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @return bool
     */
    private function optionalAttributesProvided(RoutePath $path, array $attributes): bool
    {
        $pathAttributes = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $path->getAttributes());

        $specifiedAttributes = array_keys($attributes);

        $unfilledAttributes = array_intersect($pathAttributes, $specifiedAttributes);

        return !empty($unfilledAttributes);
    }
}
