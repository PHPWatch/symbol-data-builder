<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionFunction;

class FunctionsListSource extends DataSourceBase implements DataSource {
    const NAME = 'function';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleFunctionList($this->data, $output);
    }

    private static function handleFunctionList(array $functionList, Output $output) {
        $filteredFunctions = array();

        foreach ($functionList as $name) {
            $reflection = new ReflectionFunction($name);
            if ($reflection->isUserDefined()) {
                continue;
            }

            $filteredFunctions[$name] = $name;

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/functions/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
            } else {
                // embed generic meta data
                $meta = array(
                    'type' => 'function',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => array(),
                    'deprecated' => $reflection->isDeprecated(),
                    'resources' => static::generateResources($name),
                );
            }

            $returnType = null;
            if (PHP_VERSION_ID >= 70000 && $returnType = $reflection->getReturnType()) {
                $returnType = array(
                    'type' => get_class($returnType),
                    'nullable' => $returnType->allowsNull(),
                );
            }


            $info = array(
                'type' => 'function',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'doc' => $reflection->getDocComment(),
                'parameters' => array(), // #todo
                'return' => $returnType,
                'extension' => $reflection->getExtensionName(),
                'toString' => $reflection->__toString(),
            );

            if (PHP_VERSION_ID >= 80000) {
                $fnAttrData = array();
                $fnAttrs = $reflection->getAttributes();
                foreach ($fnAttrs as $attr) {
                    $fnAttrData['__self'][] = array(
                        'attribute' => $attr->getName(),
                        'params' => $attr->getArguments(),
                    );
                }

                $params = $reflection->getParameters();
                foreach ($params as $param) {
                    $paramAttrs = $param->getAttributes();
                    foreach ($paramAttrs as $attr) {
                        $fnAttrData['params'][$param->getName()][] = array(
                            'attribute' => $attr->getName(),
                            'params' => $attr->getArguments(),
                        );
                    }
                }

                if ($fnAttrData) {
                    $info['attributes'] = $fnAttrData;
                }
            }

            $output->addData('functions/' . $filename, $info);
        }

        $output->addData('function', $functionList, true);
    }

    private static function generateResources($name) {
        return array(
            array(
                'name' => $name . ' function (php.net)',
                'url' => 'https://www.php.net/manual/function.' . str_replace('_', '-', strtolower($name)) . '.php',
            ),
        );
    }
}
