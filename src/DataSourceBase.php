<?php

namespace PHPWatch\SymbolData;

abstract class DataSourceBase implements DataSourceInterface {
    abstract protected function gatherData();
    public static function getAllData() {
        return (new static())->gatherData();
    }
}
