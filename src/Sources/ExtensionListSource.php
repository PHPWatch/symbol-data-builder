<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionExtension;

class ExtensionListSource extends DataSourceBase implements DataSource {
    const NAME = 'ext';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleExtensionList($this->data, $output);
    }

    private static function handleExtensionList(array $extList, Output $output) {
        $output->addData('ext', $extList, true);

        foreach ($extList as $name) {
            $reflection = new ReflectionExtension($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/extensions/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
            } else {
                // embed generic meta data
                $meta = array(
                    'type' => 'extension',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => array(),
                    'added' => '0.0',
                    'deprecated' => $reflection,
                    'removed' => null,
                    'version' => $reflection->getVersion(),
                    'resources' => static::generateResources($name),
                );
            }

            $entries = array(
                'classes' => $reflection->getClassNames(),
                'functions' => array(),
                'constants' => $reflection->getConstants(),
                'dependencies' => array(),
                'ini' => $reflection->getINIEntries(),
            );

            if (!empty($entries['constants'])) {
                foreach ($entries['constants'] as $constName => &$constValue) {
                    if (!empty(ConstantsSource::$dynamicConstants[$constName])) {
                        $constValue = '__DYNAMIC__';
                    }
                }
                unset($constValue);
            }

            $functions = $reflection->getFunctions();
            foreach ($functions as $function) {
                $entries['functions'][$function->getName()] = $function->getName();
            }

            $output->addData('extensions/' . $filename, array(
                'type' => 'extension',
                'name' => $reflection->getName(),
                'meta' => $meta,
            ) + $entries);
        }
    }

    private static function generateResources($extName) {
        // ignore extensions without manual entry
        if (in_array($extName, array(
            'Core',
            'standard',
        ))) {
            return array();
        }

        return array(
            array(
                'name' => $extName . ' extension (php.net)',
                'url' => 'https://www.php.net/manual/book.' . str_replace('\\', '-', strtolower($extName)) . '.php',
            ),
        );
    }
}
