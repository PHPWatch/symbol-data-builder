<?php

namespace PHPWatch\SymbolData\Sources;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\DataSourceBase;
use PHPWatch\SymbolData\Output;
use ReflectionExtension;

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
        $extList = get_loaded_extensions();

        $indexedInis = array();

        foreach ($extList as $name) {
            $reflection = new ReflectionExtension($name);
            $extIniEntries = $reflection->getINIEntries();
            $indexedInis[$name] = $extIniEntries;
        }

        $iniListPool = $iniList;

        foreach ($indexedInis as $ext => $extIniList) {
            foreach ($extIniList as $iniName => $noOp) {
                $indexedInis[$ext][$iniName] = $iniList[$iniName];
                unset($iniListPool[$iniName]);
            }
        }

        if (!empty($iniListPool)) {
            throw new \RuntimeException('Uncategorized INI entries: '. print_r($iniListPool, true));
        }

        $output->addData('ini', $indexedInis);
        $iniContents = self::getOriginIniFiles();
        $output->addData('ini/development.ini', $iniContents['development']);
        $output->addData('ini/production.ini', $iniContents['production']);
    }

    private static function getOriginIniFiles() {
        $return = array();
        $version = explode('.', PHP_VERSION);
        $version = $version[0] . '.'. $version[1];

        if ($version === '8.5') {
            $branch = 'master';
        }
        else {
            $branch = 'PHP-' . $version;
        }

        $devContents = file_get_contents('https://raw.githubusercontent.com/php/php-src/'. $branch .'/php.ini-development');
        $prodContents = file_get_contents('https://raw.githubusercontent.com/php/php-src/'. $branch .'/php.ini-production');

        $return['development'] = $devContents;
        $return['production'] = $prodContents;

        return $return;
    }
}
