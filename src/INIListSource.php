<?php

namespace PHPWatch\SymbolData;

class INIListSource extends DataSourceBase {
    const NAME = 'ini';

    public static function handleIniList(array $iniList, Output $output)
    {
        $output->addData('ini', $iniList);
    }
}
