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

            $output->addData('interfaces/' . $filename, [
                'type' => 'interface',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => [], // #todo
                'constants' => [], // #todo
                'properties' => [], // #todo
                'methods' => [], // #todo
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
