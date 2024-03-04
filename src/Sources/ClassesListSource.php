<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionClass;

class ClassesListSource extends DataSourceBase implements DataSource {
    public const NAME = 'class';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output): void {
        static::handleClassList($this->data, $output);
    }

    private static function handleClassList(array $classList, Output $output): void {
        $output->addData('class', $classList, true);

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

            $output->addData('classes/' . $filename, [
                'type' => 'class',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'traits' => $reflection->getTraitNames(),
                'is_abstract' => $reflection->isAbstract(),
                'is_anonymous' => $reflection->isAnonymous(),
                'is_cloneable' => $reflection->isCloneable(),
                'is_final' => $reflection->isFinal(),
                'is_read_only' => (method_exists($reflection, 'isReadOnly')) ? $reflection->isReadOnly() : false,
            ]);
        }
    }

    private static function generateResources(string $classname): array {
        // ignore classes without manual entry, currently only __PHP_Incomplete_Class
        if ($classname === '__PHP_Incomplete_Class') {
            return [];
        }

        return [
            [
                'name' => $classname . ' class (php.net)',
                'url' => 'https://www.php.net/manual/class.' . str_replace('\\', '-', strtolower($classname)) . '.php',
            ],
        ];
    }
}
