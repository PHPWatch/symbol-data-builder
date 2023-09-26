<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class TraitsListSource extends DataSourceBase {
    const NAME = 'trait';

    public static function handleTraitList(array $traitList, Output $output)
    {
        foreach ($traitList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/traits/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'trait',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => [],
                ];
            }

            $output->addData('traits/' . $filename, [
                'type' => 'trait',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => [], // #todo
                'constants' => [], // #todo
                'properties' => [], // #todo
                'traits' => [], // #todo
                'methods' => [], // #todo
            ]);
        }
    }

    protected function gatherData() {
        return get_declared_traits();
    }
}
