<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

/**
 * URI of a route path.
 */
class PathUri extends PathVisitor
{
    /**
     * The route path.
     *
     * @var RoutePath
     */
    private $path;

    /**
     * Provided attribute values.
     *
     * @var array
     */
    private $attributes;

    /**
     * The underlying URI.
     *
     * @var string
     */
    private $uri;

    /**
     * Flag signifying the path generation is done.
     *
     * @var bool
     */
    private $done;

    /**
     * Constructor.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @throws InvalidAttribute
     */
    public function __construct(RoutePath $path, array $attributes = [])
    {
        $this->path = $path;
        $this->attributes = $attributes;
        $this->uri = "";
        $this->done = false;

        $this->path->accept($this);
    }

    /**
     * Converts the URI to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->uri;
    }

    /**
     * Adds the attribute value to the URI.
     *
     * @param AttributePath $path
     * @throws InvalidAttribute
     */
    public function enterAttribute(AttributePath $path): void
    {
        if ($this->done) {
            return;
        }

        $name = $path->getName();
        $value = $this->getValue($path, $this->attributes);
        $pattern = $path->getTypePattern();

        $this->validateValue($name, $value, $pattern);

        $this->uri .= $value;
    }

    /**
     * Determines if the optional path should be present in the URI.
     *
     * @param OptionalPath $path
     */
    public function enterOptional(OptionalPath $path): void
    {
        if (!$this->optionalAttributesProvided($path)) {
            $this->done = true;
        }
    }

    /**
     * Adds the static part of the path to the URI.
     *
     * @param StaticPath $path
     */
    public function enterStatic(StaticPath $path): void
    {
        if ($this->done) {
            return;
        }

        $this->uri .= $path->getStatic();
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
     * @return bool
     */
    private function optionalAttributesProvided(RoutePath $path): bool
    {
        $pathAttributes = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $path->getAttributes());

        $specifiedAttributes = array_keys($this->attributes);

        $unfilledAttributes = array_intersect($pathAttributes, $specifiedAttributes);

        return !empty($unfilledAttributes);
    }
}
