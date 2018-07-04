<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Generator;

use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\Route\Attribute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\PathVisitor;
use Svoboda\PsrRouter\Route\Path\RoutePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

/**
 * Builds the URI based on the route path and provided attributes.
 */
class UriBuilder extends PathVisitor
{
    /**
     * The type context.
     *
     * @var Context
     */
    private $context;

    /**
     * Constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Builds the URI for route with given attributes.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     */
    public function buildUri(RoutePath $path, array $attributes = []): string
    {
        $data = [
            "uri" => "",
            "done" => false,
            "attributes" => $attributes,
        ];

        $path->accept($this, $data);

        return $data["uri"];
    }

    /**
     * Adds the attribute value to the URI.
     *
     * @param AttributePath $path
     * @param null $data
     * @throws InvalidAttribute
     */
    public function enterAttribute(AttributePath $path, &$data = null): void
    {
        if ($data["done"]) {
            return;
        }

        $name = $path->getName();
        $value = $this->getValue($path, $data["attributes"]);
        $pattern = $this->getTypePattern($path);

        $this->validateValue($name, $value, $pattern);

        $data["uri"] .= $value;
    }

    /**
     * Determines if the optional path should be present in the URI.
     *
     * @param OptionalPath $path
     * @param null $data
     */
    public function enterOptional(OptionalPath $path, &$data = null): void
    {
        if (!$this->optionalAttributesProvided($path, $data["attributes"])) {
            $data["done"] = true;
        }
    }

    /**
     * Adds the static part of the path to the URI.
     *
     * @param StaticPath $path
     * @param null $data
     */
    public function enterStatic(StaticPath $path, &$data = null): void
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

        return (string) $attributes[$name];
    }

    /**
     * Returns the regular expression for the attribute type.
     *
     * @param AttributePath $path
     * @return string
     * @throws InvalidAttribute
     */
    private function getTypePattern(AttributePath $path): string
    {
        $type = $path->getType() ?? $this->context->getImplicitType();
        $typePatterns = $this->context->getTypePatterns();

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
