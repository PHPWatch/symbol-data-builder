<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionClass;

class AttributesListSource extends DataSourceBase implements DataSource {
    const NAME = 'attribute';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleAttributeList($this->data, $output);
    }

    private static function handleAttributeList(array $attributeList, Output $output) {
        $output->addData('attribute', $attributeList, true);

        foreach ($attributeList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/attributes/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
            } else {
                // embed generic meta data
                $meta = array(
                    'type' => 'attribute',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => array(),
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($name),
                );
            }

            $output->addData('attributes/' . $filename, array(
                'type' => 'attribute',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'traits' => PHP_VERSION_ID >= 50400 ? $reflection->getTraitNames() : null,
            ));
        }
    }

    private static function generateResources($classname) {
        return array(
            array(
                'name' => $classname . ' attribute (php.net)',
                'url' => 'https://www.php.net/manual/class.' . strtolower($classname) . '.php',
            ),
        );
    }
}
