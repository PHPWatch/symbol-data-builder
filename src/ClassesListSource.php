<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class ClassesListSource extends DataSourceBase {
    const NAME = 'class';

    public static function handleClassList(array $classList, Output $output)
    {
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
                    'resources' => [],
                ];
            }

            $output->addData('classes/' . $filename, [
                'type' => 'class',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => [], // #todo
                'constants' => [], // #todo
                'properties' => [], // #todo
                'methods' => [], // #todo
            ]);
        }
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
