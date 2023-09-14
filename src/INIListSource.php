<?php

namespace PHPWatch\SymbolData;

class INIListSource extends DataSourceBase {
    const NAME = 'ini';
    protected function gatherData() {
        return ini_get_all();
    }
}
