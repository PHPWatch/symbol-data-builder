<?php

namespace PHPWatch\SymbolData;

class INIListSource extends DataSourceBase implements DataSource {
    const NAME = 'ini';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output): void
    {
        static::handleIniList($this->data, $output);
    }

    public static function handleIniList(array $iniList, Output $output)
    {
        $output->addData('ini', $iniList);
    }
}
