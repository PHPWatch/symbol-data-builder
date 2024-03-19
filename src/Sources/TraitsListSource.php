<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionClass;

class TraitsListSource extends DataSourceBase implements DataSource {
    const NAME = 'trait';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleTraitList($this->data, $output);
    }

    private static function handleTraitList(array $traitList, Output $output) {
        $output->addData('trait', $traitList, true);

        foreach ($traitList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/traits/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
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

            $output->addData('traits/' . $filename, array(
                'type' => 'trait',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'traits' => PHP_VERSION_ID >= 50400 ? $reflection->getTraitNames() : null,
                'is_abstract' => $reflection->isAbstract(),
                'is_anonymous' => PHP_VERSION_ID >= 70000 ? $reflection->isAnonymous() : null,
                'is_cloneable' => PHP_VERSION_ID >= 50400 ? $reflection->isCloneable() : null,
                'is_final' => $reflection->isFinal(),
                'is_read_only' => (method_exists($reflection, 'isReadOnly')) ? $reflection->isReadOnly() : false,
            ));
        }
    }
}
