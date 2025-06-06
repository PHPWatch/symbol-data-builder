<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionClass;

class InterfacesListSource extends DataSourceBase implements DataSource {
    const NAME = 'interface';

    const NAME_PLURAL = 'interfaces';

    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleClassLikeList($this->data, $output);
    }


    private static function handleClassLikeList(array $classLikeList, Output $output) {
        $output->addData(static::NAME, $classLikeList, true);

        foreach ($classLikeList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../../meta/' . static::NAME_PLURAL . '/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = require $metafile;
            } else {
                // embed generic meta data
                $meta = array(
                    'type' => static::NAME,
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => array(),
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($name),
                );
            }



            $output->addData(static::NAME_PLURAL . '/' . $filename, array(
                'type' => static::NAME,
                'name' => $reflection->getName(),
                'meta' => $meta,
                'comment' => $reflection->getDocComment(),
                'namespace' => $reflection->getNamespaceName(),
                'constants' => self::reflectClassConstants($reflection->getConstants(), $name),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'interfaces' => $reflection->getInterfaceNames(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'extension' => $reflection->getExtensionName(),
                'toString' => $reflection->__toString(),
            ) + static::getAdditionalData($reflection));
        }
    }

    protected static function getAdditionalData(ReflectionClass $reflection) {
        $additional = array();

        if (PHP_VERSION_ID >= 80000) {
            $attrs  = $reflection->getAttributes();
            if ($attrs) {
                $additional['attributes'] = array();
                foreach ($attrs as $attr) {
                    $additional['attributes'][] = $attr->getName();
                }
            }
        }

        return $additional;
    }

    protected static function reflectClassConstants(array $constants, $className) {
        $return = array();
        foreach ($constants as $name => $value) {
            $constVals = array(
                'value' => $value,
            );

            if (PHP_VERSION_ID >= 70100) {
                $reflector = new \ReflectionClassConstant($className, $name);
                $constVals['toString'] = $reflector->__toString();
                $constVals['visibility'] = $reflector->getModifiers();

                if (PHP_VERSION_ID >= 80100) {
                    $constVals['isFinal'] = $reflector->isFinal();

                    if ($reflector->isEnumCase()) {
                        $constVals['value'] = null;
                        $constVals['isEnumCase'] = true;
                    }
                }

                if (PHP_VERSION_ID >= 80300 && $reflector->hasType()) {
                    $constVals['type'] = $reflector->getType()->getName();
                }

                if (PHP_VERSION_ID >= 80400 && $reflector->isDeprecated()) {
                    $constVals['isDeprecated'] = true;
                }
            }
            $return[$name] = $constVals;
        }

        return $return;
    }

    protected static function generateResources($name) {
        return array(
            array(
                'name' => $name . ' '. self::NAME .' (php.net)',
                'url' => 'https://www.php.net/manual/class.' . str_replace('\\', '-', strtolower($name)) . '.php',
            ),
        );
    }
}
