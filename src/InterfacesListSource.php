<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class InterfacesListSource extends DataSourceBase {
    const NAME = 'interface';

    public static function handleInterfaceList(array $interfaceList, Output $output)
    {
        $output->addData('interface', $interfaceList);

        foreach ($interfaceList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/interfaces/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'interface',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($name),
                ];
            }

            $properties = [];

            foreach ($reflection->getProperties() as $property) {
                $properties[$property->getName()] = [
                    'name' => $property->getName(),
                    'class' => $property->getDeclaringClass()->getName(),
                    'type' => ($property->getType() !== null) ? strval($property->getType()) : null,
                    'has_default_value' => $property->hasDefaultValue(),
                    'default_value' => $property->getDefaultValue(),
                    'is_static' => $property->isStatic(),
                    'is_public' => $property->isPublic(),
                    'is_protected' => $property->isProtected(),
                    'is_private' => $property->isPrivate(),
                    'is_promoted' => $property->isPromoted(),
                ];
            }

            $methods = [];

            foreach ($reflection->getMethods() as $method) {
                $parameters = [];

                foreach($method->getParameters() as $parameter) {
                    $parameters[$parameter->getName()] = [
                        'position'=> $parameter->getPosition(),
                        'name'=> $parameter->getName(),
                        'type'=> ($parameter->getType() !== null) ? strval($parameter->getType()) : null,
                        'is_optional' => $parameter->isOptional(),
                        'has_default_value' => $parameter->isDefaultValueAvailable(),
                        'default_value' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                        'has_default_value_constant' => $parameter->isDefaultValueAvailable() && $parameter->isDefaultValueConstant(),
                        'default_value_constant' => $parameter->isDefaultValueAvailable() && $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValueConstantName() : null,
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

            $output->addData('interfaces/' . $filename, [
                'type' => 'interface',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => $properties,
                'methods' => $methods,
            ]);
        }
    }

    private static function generateResources(string $name): array
    {
        return [
            [
                'name' => $name . ' interface (php.net)',
                'url' => 'https://www.php.net/manual/class.' . str_replace('\\', '-', strtolower($name)) . '.php',
            ],
        ];
    }

    protected function gatherData() {
        $interfaces = get_declared_interfaces();
        $return = [];

        foreach ($interfaces as $interface) {
            if (strpos($interface, 'Composer\\') === 0) {
                continue;
            }

            if (strpos($interface, 'PHPWatch\\') === 0) {
                continue;
            }

            $return[] = $interface;
        }

        return $return;
    }
}
