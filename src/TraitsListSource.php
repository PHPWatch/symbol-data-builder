<?php

namespace PHPWatch\SymbolData;
class TraitsListSource extends DataSourceBase {
    const NAME = 'trait';
    protected function gatherData() {
        return get_declared_traits();
    }
}
