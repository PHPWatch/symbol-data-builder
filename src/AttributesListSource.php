<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class AttributesListSource extends DataSourceBase {
    const NAME = 'attribute';

    public static function handleAttributeList(array $attributeList, Output $output)
    {
        $output->addData('attribute', $attributeList);

        foreach ($attributeList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/attributes/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'attribute',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($name),
                ];
            }

            $output->addData('attributes/' . $filename, [
                'type' => 'attribute',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'traits' => $reflection->getTraitNames(),
            ]);
        }
    }

    private static function generateResources(string $classname): array
    {
        return [
            [
                'name' => $classname . ' attribute (php.net)',
                'url' => 'https://www.php.net/manual/class.' . strtolower($classname) . '.php',
            ],
        ];
    }

    protected function gatherData() {
        if (!class_exists('Attribute', false)) {
            return [];
        }

        $classes = get_declared_classes();
        $return = [];

        foreach ($classes as $class) {
            if ($class === 'Attribute') {
                continue;
            }

            $reflector = new ReflectionClass($class);
            $attributes = $reflector->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() === 'Attribute') {
                    $return[] = $class;
                }
            }
        }

        return $return;
    }
}
