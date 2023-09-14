<?php

namespace PHPWatch\SymbolData;
class TraitsListSource implements DataSourceInterface {
    const NAME = 'trait';
    private function gatherData() {
        return get_declared_traits();
    }
    public static function getAllData() {
        return (new self())->gatherData();
    }
}
