<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class AttributesListSource extends DataSourceBase {
    const NAME = 'attribute';
    protected function gatherData() {
        if (!class_exists('Attribute', false)) {
            return [];
        }

        $classes = get_declared_classes();
        $return = [];

        foreach ($classes as $class) {
            if ($class === 'Attribute') {
                continue;
            }

            $reflector = new ReflectionClass($class);
            $attributes = $reflector->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() === 'Attribute') {
                    $return[] = $class;
                }
            }
        }

        return $return;
    }
}
