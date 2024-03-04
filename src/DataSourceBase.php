<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

abstract class DataSourceBase implements DataSourceInterface {
    /**
     * @deprecated
     */
    public static function getAllData() {
        return [];
    }

    protected static function generateDetailsAboutMethods(ReflectionClass $reflectionClass)
    {
        $methods = [];

        foreach ($reflectionClass->getMethods() as $method) {
            $parameters = [];

            foreach ($method->getParameters() as $parameter) {
                $parameters[$parameter->getName()] = [
                    'position' => $parameter->getPosition(),
                    'name' => $parameter->getName(),
                    'type' => ($parameter->getType() !== null) ? (string) $parameter->getType() : null,
                    'is_optional' => $parameter->isOptional(),
                    'has_default_value' => $parameter->isDefaultValueAvailable(),
                    'default_value' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                    'has_default_value_constant' => $parameter->isDefaultValueAvailable()
                        && $parameter->isDefaultValueConstant(),
                    'default_value_constant' =>
                        $parameter->isDefaultValueAvailable()
                            ? $parameter->getDefaultValueConstantName()
                            : null,
                ];
            }

            $methods[$method->getName()] = [
                'name' => $method->getName(),
                'class' => $method->getDeclaringClass()->getName(),
                'parameters' => $parameters,
                'return_type' => ($method->getReturnType() !== null) ? strval($method->getReturnType()) : null,
                'has_return_type' => $method->hasReturnType(),
                'is_static' => $method->isStatic(),
                'is_public' => $method->isPublic(),
                'is_protected' => $method->isProtected(),
                'is_private' => $method->isPrivate(),
            ];
        }

        return $methods;
    }

    protected static function generateDetailsAboutProperties(ReflectionClass $reflectionClass)
    {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $properties[$property->getName()] = [
                'name' => $property->getName(),
                'class' => $property->getDeclaringClass()->getName(),
                'type' => (method_exists($property, 'getType') && $property->getType() !== null) ? strval($property->getType()) : null,
                'has_default_value' => (method_exists($property, 'hasDefaultValue')) ? $property->hasDefaultValue() : false,
                'default_value' => (method_exists($property, 'getDefaultValue')) ? $property->getDefaultValue() : null,
                'is_static' => $property->isStatic(),
                'is_public' => $property->isPublic(),
                'is_protected' => $property->isProtected(),
                'is_private' => $property->isPrivate(),
                'is_promoted' => (method_exists($property, 'isPromoted')) ? $property->isPromoted() : false,
            ];
        }

        return $properties;
    }
}
