<?php

namespace PHPWatch\SymbolData;

class INIListSource extends DataSourceBase implements DataSource {
    const NAME = 'ini';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output): void {
        static::handleIniList($this->data, $output);
    }

    private static function handleIniList(array $iniList, Output $output): void {
        $output->addData('ini', $iniList);
    }
}
