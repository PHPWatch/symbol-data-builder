<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class ClassesListSource extends DataSourceBase {
    const NAME = 'class';

    public static function handleClassList(array $classList, Output $output)
    {
        $output->addData('class', $classList);

        foreach ($classList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/classes/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'class',
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

            $output->addData('classes/' . $filename, [
                'type' => 'class',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => $properties,
                'traits' => $reflection->getTraitNames(),
                'methods' => $methods,
                'is_abstract' => $reflection->isAbstract(),
                'is_anonymous' => $reflection->isAnonymous(),
                'is_cloneable' => $reflection->isCloneable(),
                'is_final' => $reflection->isFinal(),
                'is_read_only' => (method_exists($reflection, 'isReadOnly')) ? $reflection->isReadOnly() : false,
            ]);
        }
    }

    private static function generateResources(string $classname): array
    {
        // ignore classes without manual entry
        if (in_array($classname, [
            '__PHP_Incomplete_Class',
        ])) {
            return [];
        }

        return [
            [
                'name' => $classname . ' class (php.net)',
                'url' => 'https://www.php.net/manual/class.' . str_replace('\\', '-', strtolower($classname)) . '.php',
            ],
        ];
    }

    protected function gatherData() {
        $classes = get_declared_classes();
        $return = [];

        foreach ($classes as $class) {
            if (strpos($class, 'Composer\\') === 0) {
                continue;
            }

            if (strpos($class, 'ComposerAutoloaderInit') === 0) {
                continue;
            }

            if (strpos($class, 'PHPWatch\\') === 0) {
                continue;
            }

            $return[] = $class;
        }

        return $return;
    }
}
