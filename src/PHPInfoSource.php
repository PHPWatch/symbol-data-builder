<?php

namespace PHPWatch\SymbolData;

class PHPInfoSource extends DataSourceBase {
    const NAME = 'phpinfo';

    private function postProcess(string $output): string {
        $re = '/^(Compiled|Build date)( => )(?<dynamic>.*?)$/m';
        $subst = "$1$2__DYNAMIC__";
        return preg_replace($re, $subst, $output);
    }

    protected function gatherData() {
        ob_start();
        // Do not include env of build info as they change in every build and run
        phpinfo(INFO_CREDITS|INFO_LICENSE|INFO_MODULES|INFO_CONFIGURATION);
        $return = ob_get_clean();
        return $this->postProcess($return);
    }
}
