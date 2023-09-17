<?php

namespace PHPWatch\SymbolData;

class ClassesListSource extends DataSourceBase {
    const NAME = 'class';
    protected function gatherData() {
        $classes = get_declared_classes();
        $return = [];

        foreach ($classes as $class) {
            if (strpos($class, 'Composer\\') === 0) {
                continue;
            }

            if (strpos($class, 'ComposerAutoloaderInit') === 0) {
                continue;
            }

            if (strpos($class, 'PHPWatch\\') === 0) {
                continue;
            }

            $return[] = $class;
        }

        return $return;
    }
}
