<?php

namespace PHPWatch\SymbolData;

class FunctionsListSource extends DataSourceBase {
    const NAME = 'function';
    protected function gatherData() {
        return get_defined_functions()['internal'];
    }
}
