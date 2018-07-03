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

class UriBuilder extends PathVisitor
{
    /**
     * The URI.
     *
     * @var string
     */
    private $uri;

    /**
     * True if all given attributes were already put into the URI.
     *
     * @var bool
     */
    private $done;

    /**
     * Route path.
     *
     * @var RoutePath
     */
    private $path;

    /**
     * Path attributes.
     *
     * @var array
     */
    private $attributes;

    /**
     * Builds the URI for route with given attributes.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @param Context $context
     * @return string
     * @throws InvalidAttribute
     */
    public function buildUri(RoutePath $path, array $attributes, Context $context): string
    {
        $this->uri = "";
        $this->done = false;
        $this->path = $path;
        $this->attributes = $attributes;

        $this->checkAttributesExist($attributes);
        $this->checkRequiredAttributesExist($attributes);
        $this->checkOptionalAttributesAreContinuous($attributes);
        $this->checkAttributesType($attributes, $context);

        $path->accept($this);

        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path): void
    {
        if ($this->done) {
            return;
        }

        $name = $path->getName();

        $this->uri .= $this->attributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path): void
    {
        $pathAttributes = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $path->getAttributes());

        $specifiedAttributes = array_keys($this->attributes);

        $unfilledAttributes = array_intersect($pathAttributes, $specifiedAttributes);

        if (empty($unfilledAttributes)) {
            $this->done = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path): void
    {
        if ($this->done) {
            return;
        }

        $this->uri .= $path->getStatic();
    }

    /**
     * Checks that all provided attributes exist in the route path.
     *
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkAttributesExist(array $attributes): void
    {
        $pathAttributes = $this->path->getAttributes();

        $pathAttributeNames = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $pathAttributes);

        foreach ($attributes as $name => $value) {
            if (!in_array($name, $pathAttributeNames)) {
                throw new InvalidAttribute("The attribute does not exist in path specification.");
            }
        }
    }

    /**
     * Checks that all required attributes are provided.
     *
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkRequiredAttributesExist(array $attributes): void
    {
        $pathAttributes = $this->path->getAttributes();

        $requiredAttributes = array_filter($pathAttributes, function (Attribute $attribute) {
            return $attribute->isRequired();
        });

        foreach ($requiredAttributes as $requiredAttribute) {
            $name = $requiredAttribute->getName();

            if (!array_key_exists($name, $attributes)) {
                throw new InvalidAttribute("Required attribute not supplied.");
            }
        }
    }

    /**
     * Checks that if optional attribute is provided, all preceding attributes
     * are also provided.
     *
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkOptionalAttributesAreContinuous(array $attributes): void
    {
        $pathAttributes = $this->path->getAttributes();

        $optionalAttributes = array_filter($pathAttributes, function (Attribute $attribute) {
            return !$attribute->isRequired();
        });

        $optionalSkipped = false;

        foreach ($optionalAttributes as $optionalAttribute) {
            $name = $optionalAttribute->getName();

            if ($optionalSkipped && array_key_exists($name, $attributes)) {
                throw new InvalidAttribute("If you specify optional attribute, you also have to specify all preceding attributes.");
            }

            if (!array_key_exists($name, $attributes)) {
                $optionalSkipped = true;
            }
        }
    }

    /**
     * Checks that all provided attributes have the correct type.
     *
     * @param array $attributes
     * @param Context $context
     * @throws InvalidAttribute
     */
    private function checkAttributesType(array $attributes, Context $context): void
    {
        $pathAttributes = $this->path->getAttributes();

        foreach ($pathAttributes as $pathAttribute) {
            $name = $pathAttribute->getName();
            $type = $pathAttribute->getType() ?? $context->getImplicitType();

            if (!array_key_exists($name, $attributes)) {
                continue;
            }

            $typePatterns = $context->getTypePatterns();

            if (!array_key_exists($type, $typePatterns)) {
                throw new InvalidAttribute("Attribute type does not exist in the context.");
            }

            $typePattern = $typePatterns[$type];

            $value = (string) $attributes[$name];

            $matches = [];

            if (!preg_match("#" . $typePattern . "#", $value, $matches)) {
                throw new InvalidAttribute("Attribute does not conform to type pattern.");
            }
        }
    }
}
