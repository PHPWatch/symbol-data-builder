<?php

namespace PHPWatch\SymbolData;

class ExtensionListSource implements DataSourceInterface {
    const NAME = 'ext';
    private function gatherData() {
        return get_loaded_extensions();
    }
    public static function getAllData() {
        return (new self())->gatherData();
    }
}
