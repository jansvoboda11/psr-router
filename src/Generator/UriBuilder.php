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
        $data = [
            "uri" => "",
            "done" => false,
            "attributes" => $attributes,
            "context" => $context,
        ];

        $this->checkAttributesExist($path, $attributes);
        $this->checkRequiredAttributesExist($path, $attributes);
        $this->checkOptionalAttributesAreContinuous($path, $attributes);
        $this->checkAttributesType($path, $attributes, $context);

        $path->accept($this, $data);

        return $data["uri"];
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path, &$data = null): void
    {
        if ($data["done"]) {
            return;
        }

        $name = $path->getName();

        $data["uri"] .= $data["attributes"][$name];
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path, &$data = null): void
    {
        $pathAttributes = array_map(function (Attribute $attribute) {
            return $attribute->getName();
        }, $path->getAttributes());

        $specifiedAttributes = array_keys($data["attributes"]);

        $unfilledAttributes = array_intersect($pathAttributes, $specifiedAttributes);

        if (empty($unfilledAttributes)) {
            $data["done"] = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path, &$data = null): void
    {
        if ($data["done"]) {
            return;
        }

        $data["uri"] .= $path->getStatic();
    }

    /**
     * Checks that all provided attributes exist in the route path.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkAttributesExist(RoutePath $path, array $attributes): void
    {
        $pathAttributes = $path->getAttributes();

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
     * @param RoutePath $path
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkRequiredAttributesExist(RoutePath $path, array $attributes): void
    {
        $pathAttributes = $path->getAttributes();

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
     * @param RoutePath $path
     * @param array $attributes
     * @throws InvalidAttribute
     */
    private function checkOptionalAttributesAreContinuous(RoutePath $path, array $attributes): void
    {
        $pathAttributes = $path->getAttributes();

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
     * @param RoutePath $path
     * @param array $attributes
     * @param Context $context
     * @throws InvalidAttribute
     */
    private function checkAttributesType(RoutePath $path, array $attributes, Context $context): void
    {
        $pathAttributes = $path->getAttributes();

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
