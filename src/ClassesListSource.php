<?php

namespace PHPWatch\SymbolData;

class ClassesListSource implements DataSourceInterface {
    const NAME = 'class';
    private function gatherData() {
        $classes = get_declared_classes();
        $return = [];

        foreach ($classes as $class) {
            if (strpos($class, 'Composer/') === 0) {
                continue;
            }

            if (strpos($class, 'PHPWatch/') === 0) {
                continue;
            }

            $return[] = $class;
        }

        return $return;
    }

    public static function getAllData() {
        return (new self())->gatherData();
    }
}
