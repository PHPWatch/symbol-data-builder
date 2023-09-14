<?php

namespace PHPWatch\SymbolData;

class InterfacesListSource implements DataSourceInterface {
    const NAME = 'interface';
    private function gatherData() {
        return get_declared_interfaces();
    }
    public static function getAllData() {
        return (new self())->gatherData();
    }
}
