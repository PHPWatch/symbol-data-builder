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

    protected function gatherData() {
        ob_start();
        // Do not include env of build info as they change in every build and run
        phpinfo(INFO_CREDITS|INFO_LICENSE|INFO_MODULES|INFO_CONFIGURATION);
        $return = ob_get_clean();
        return static::postProcess($return);
    }
}
