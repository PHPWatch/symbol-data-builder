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

    private static $coreExtensionsAllowList = array(
        'dom', 'mysqlnd', 'zip',
    );

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleExtensionList($this->data, $output);
    }

    private static function handleExtensionList(array $extList, Output $output) {
        $output->addData('ext', $extList, true);

        $extListFile = __DIR__ . '/../../meta/core-exts/' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '/ext.php';

        if (!file_exists($extListFile)) {
            throw new \Exception('Extension list file does not exist');
        }

        $extList = require $extListFile;

        foreach ($extList as $name) {
            $external = false;
            $reflection = new ReflectionExtension($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/extensions/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
            } else {
                $extVersion = $reflection->getVersion();

                if ($extVersion === PHP_VERSION || $extVersion !== PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION) {
                    $extVersion = '__DYNAMIC__PHP Version';
                }

                $external = !in_array($name, $extList, true);

                // embed generic meta data
                $meta = array(
                    'type' => 'extension',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => array(),
                    'added' => '0.0',
                    'deprecated' => $reflection,
                    'removed' => null,
                    'version' => $extVersion,
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
                $functionName = $function->getName();
                $entries['functions'][$functionName] = $functionName;
            }

            $stub = array(
                'type' => 'extension',
                'name' => $reflection->getName(),
            );

            if ($external) {
                var_dump($name);
                $stub['external'] = true;
            }

            $output->addData('extensions/' . $filename, $stub + array(
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
