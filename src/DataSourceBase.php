<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

abstract class DataSourceBase implements DataSourceInterface {
    protected static function generateDetailsAboutMethods(ReflectionClass $reflectionClass) {
        $methods = array();

        foreach ($reflectionClass->getMethods() as $method) {
            $parameters = array();
            $methodName = $method->getName();

            foreach ($method->getParameters() as $parameter) {
                $paramName = $parameter->getName();
                $parameters[$paramName] = array(
                    'position' => $parameter->getPosition(),
                    'name' => $paramName,
                    'type' => PHP_VERSION_ID >= 70000 ? (($parameter->getType() !== null) ? (string)$parameter->getType() : null) : null,
                    'is_optional' => $parameter->isOptional(),
                    'has_default_value' => $parameter->isDefaultValueAvailable(),
                    //'default_value' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                    'has_default_value_constant' => $parameter->isDefaultValueAvailable()
                        && (PHP_VERSION_ID >= 50400 ? $parameter->isDefaultValueConstant() : null),
                    'default_value_constant' =>
                        $parameter->isDefaultValueAvailable()
                            ? (PHP_VERSION_ID >= 50400 ? $parameter->getDefaultValueConstantName() : null)
                            : null,
                );
            }

            $methods[$methodName] = array(
                'name' => $methodName,
                'class' => $method->getDeclaringClass()->getName(),
                'parameters' => $parameters,
                'return_type' => PHP_VERSION_ID >= 70000 ? (($method->getReturnType() !== null) ? (string)$method->getReturnType() : null) : null,
                'has_return_type' => PHP_VERSION_ID >= 70000 ? $method->hasReturnType() : null,
                'is_static' => $method->isStatic(),
                'is_public' => $method->isPublic(),
                'is_protected' => $method->isProtected(),
                'is_private' => $method->isPrivate(),
            );
        }

        return $methods;
    }

    protected static function generateDetailsAboutProperties(ReflectionClass $reflectionClass) {
        $properties = array();

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $properties[$propertyName] = array(
                'name' => $propertyName,
                'class' => $property->getDeclaringClass()->getName(),
                'type' => (method_exists($property, 'getType') && $property->getType() !== null)
                    ? (string)$property->getType()
                    : null,
                'has_default_value' => method_exists($property, 'hasDefaultValue') && $property->hasDefaultValue(),
                'default_value' => (method_exists($property, 'getDefaultValue')) ? $property->getDefaultValue() : null,
                'is_static' => $property->isStatic(),
                'is_public' => $property->isPublic(),
                'is_protected' => $property->isProtected(),
                'is_private' => $property->isPrivate(),
                'is_promoted' => method_exists($property, 'isPromoted') && $property->isPromoted(),
            );
        }

        return $properties;
    }
}
