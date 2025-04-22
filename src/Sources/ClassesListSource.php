<?php

namespace PHPWatch\SymbolData\Sources;

use ReflectionClass;

class ClassesListSource extends InterfacesListSource {
    const NAME = 'class';
    const NAME_PLURAL = 'classes';

    protected static function getAdditionalData(ReflectionClass $reflection) {
        $additional = parent::getAdditionalData($reflection);
        $additional += array(
            'modifiers' => $reflection->getModifiers(),
            'defaultProperties' => $reflection->getDefaultProperties(),
            'staticProperties' => $reflection->getStaticProperties(),
            'properties' => static::generateDetailsAboutProperties($reflection),
            'traits' => PHP_VERSION_ID >= 50400 ? $reflection->getTraitNames() : null,
            'is_abstract' => $reflection->isAbstract(),
            'is_anonymous' => PHP_VERSION_ID >= 70000 ? ($reflection->isAnonymous()) : null,
            'is_cloneable' => PHP_VERSION_ID >= 50400 ? $reflection->isCloneable() : null,
            'is_final' => $reflection->isFinal(),
            'is_instantiable' => $reflection->isInstantiable(),
            'is_read_only' => (method_exists($reflection, 'isReadOnly')) ? $reflection->isReadOnly() : false,
        );

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
}
