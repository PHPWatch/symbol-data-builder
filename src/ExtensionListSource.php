<?php

namespace PHPWatch\SymbolData;

use ReflectionExtension;

class ExtensionListSource extends DataSourceBase {
    const NAME = 'ext';

    public static function handleExtensionList(array $extList, Output $output)
    {
        $output->addData('ext', $extList);

        foreach ($extList as $name) {
            $reflection = new ReflectionExtension($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/extensions/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'extension',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($name),
                ];
            }

            $output->addData('extensions/' . $filename, [
                'type' => 'extension',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'classes' => [], // #todo
                'constants' => [], // #todo
                'dependencies' => [], // #todo
                'functions' => [], // #todo
                'ini' => [], // #todo
            ]);
        }
    }

    private static function generateResources(string $extName): array
    {
        // ignore extenstions without manual entry
        if (in_array($extName, [
            'Core',
            'standard',
        ])) {
            return [];
        }

        return [
            [
                'name' => $extName . ' extension (php.net)',
                'url' => 'https://www.php.net/manual/book.' . str_replace('\\', '-', strtolower($extName)) . '.php',
            ],
        ];
    }
}
