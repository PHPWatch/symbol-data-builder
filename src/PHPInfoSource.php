<?php

namespace PHPWatch\SymbolData;

class PHPInfoSource extends DataSourceBase {
    const NAME = 'phpinfo';

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
