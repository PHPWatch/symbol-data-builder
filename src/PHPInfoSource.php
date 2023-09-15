<?php

namespace PHPWatch\SymbolData;

class PHPInfoSource extends DataSourceBase {
    const NAME = 'phpinfo';
    protected function gatherData() {
        ob_start();
        phpinfo();
        return ob_get_clean();
    }
}
