<?php

namespace PHPWatch\SymbolData;

class ClassesListSource implements DataSourceInterface {
    const NAME = 'class';
    private function gatherData() {
        return get_declared_classes();
    }
    public static function getAllData() {
        return (new self())->gatherData();
    }
}
