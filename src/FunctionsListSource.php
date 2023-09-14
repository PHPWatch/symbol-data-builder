<?php

namespace PHPWatch\SymbolData;

class FunctionsListSource implements DataSourceInterface {
    const NAME = 'function';
    private function gatherData() {
        return get_defined_functions()['internal'];
    }
    public static function getAllData() {
        return (new self())->gatherData();
    }
}
