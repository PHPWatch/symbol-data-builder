<?php

namespace PHPWatch\SymbolData;

class ExtensionListSource extends DataSourceBase {
    const NAME = 'ext';
    protected function gatherData() {
        return get_loaded_extensions();
    }
}
