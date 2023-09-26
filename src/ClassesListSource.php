<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class ClassesListSource extends DataSourceBase {
    const NAME = 'class';

    public static function handleClassList(array $classList, Output $output)
    {
        foreach ($classList as $name) {
            // Handle namespaces
            $filename = str_replace('\\', '/', $name);

            $reflection = new ReflectionClass($name);

            $output->addData('classes/' . $filename, [
                'type' => 'class',
                'name' => $reflection->getName(),
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
