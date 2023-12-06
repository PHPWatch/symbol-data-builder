<?php

namespace PHPWatch\SymbolData;

class PHPInfoSource extends DataSourceBase implements DataSource {
    const NAME = 'phpinfo';

    /**
     * @var string
     */
    private $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output): void
    {
        static::handlePhpinfoString($this->data, $output);
    }

    public static function handlePhpinfoString(string $phpinfo, Output $output)
    {
        $output->addData('phpinfo', static::postProcess($phpinfo));
    }

    private static function postProcess(string $output): string {
        $re = '/^(Compiled|Build date)( => )(?<dynamic>.*?)$/mi';
        $subst = "$1$2__DYNAMIC__";
        return preg_replace($re, $subst, $output);
    }
}
