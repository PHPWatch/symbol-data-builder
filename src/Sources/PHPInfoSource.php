<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;

class PHPInfoSource extends DataSourceBase implements DataSource {
    const NAME = 'phpinfo';

    /**
     * @var string
     */
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output) {
        static::handlePhpinfoString($this->data, $output);
    }

    private static function handlePhpinfoString($phpinfo, Output $output) {
        $output->addData('phpinfo', static::postProcess($phpinfo));
    }

    private static function postProcess($output) {
        $re = '/^(Compiled|Build date)( => )(?<dynamic>.*?)$/mi';
        $subst = "$1$2__DYNAMIC__";
        return preg_replace($re, $subst, $output);
    }
}
