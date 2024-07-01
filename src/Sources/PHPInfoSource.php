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
        // Replace "compiled date" and "build date" with __DYNAMIC__"
        $re = '/^(Compiled|Build date)( => )(?<dynamic>.*?)$/mi';
        $subst = "$1$2__DYNAMIC__";
        $output = preg_replace($re, $subst, $output);

        $regex = '@enchant support => enabled.*?Revision.*?$\n(?<libs>.*)\nereg@s';
        if (preg_match($regex, $output, $matches)) {
            $lines = preg_split('/(\r|\n|\r\n)+/', $matches['libs']);
            sort($lines);
            $lines = implode("\n", $lines);

            $output = str_replace($matches['libs'], $lines, $output);
        }


        return $output;
    }
}
