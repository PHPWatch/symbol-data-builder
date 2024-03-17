<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;

class INIListSource extends DataSourceBase implements DataSource {
    const NAME = 'ini';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handleIniList($this->data, $output);
    }

    private static function handleIniList(array $iniList, Output $output) {
        $output->addData('ini', $iniList);
    }
}
