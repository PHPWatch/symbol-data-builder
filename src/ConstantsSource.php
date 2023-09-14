<?php

namespace PHPWatch\SymbolData;

class ConstantsSource extends DataSourceBase {
    const NAME = 'const';
    protected function gatherData() {
        return get_defined_constants(true);
    }
}
