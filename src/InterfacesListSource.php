<?php

namespace PHPWatch\SymbolData;

class InterfacesListSource extends DataSourceBase {
    const NAME = 'interface';
    protected function gatherData() {
        return get_declared_interfaces();
    }
}
