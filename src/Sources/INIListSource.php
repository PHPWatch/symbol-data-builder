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

    private static $lastError = null;

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
                $deprecated = self::checkIfIniDeprecated($iniName, $iniList[$iniName]);
                if ($deprecated) {
                    $iniList[$iniName]['deprecated'] = $deprecated;
                }
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

        if (!$devContents || !$prodContents) {
            throw new \RuntimeException(sprintf('Unable to fetch INI values: %s, %s', 'https://raw.githubusercontent.com/php/php-src/'. $branch .'/php.ini-development', 'https://raw.githubusercontent.com/php/php-src/'. $branch .'/php.ini-production'));
        }

        $return['development'] = $devContents;
        $return['production'] = $prodContents;

        return $return;
    }

    private static function checkIfIniDeprecated($iniName, array $iniDef) {
        if (!($iniDef['access'] & INI_USER)) {
            return null;
        }

        $existingValue = ini_get($iniName);
        $mutatedValue = self::getMutatedIniValue($iniName, $existingValue, $iniDef);

        if ($mutatedValue === null) {
            return null;
        }

        set_error_handler(array('\PHPWatch\SymbolData\Sources\INIListSource', 'callErrorHandler'), E_ALL);
        self::clearDeprecationLastError();
        ini_set($iniName, $mutatedValue);
        ini_set($iniName, $existingValue);
        restore_error_handler();

        $message = self::getLastErrorMessage();

        if (empty($message)) {
            return false;
        }

        return stripos($message, 'deprecate')
            ? self::trimDeprecationMessage($message)
            : false;
    }

    private static function trimDeprecationMessage($message) {
        if (strpos($message, 'ini_set(): ') === 0) {
            return substr($message, 11);
        }

        return $message;
    }

    public static function callErrorHandler($noop, $message) {
        self::$lastError = $message;
    }

    protected static function clearDeprecationLastError() {
        self::$lastError = null;
    }

    private static function getMutatedIniValue($iniName, $existingValue, $iniDef) {
        if ($existingValue === "1") {
            return "0";
        }

        if ($existingValue === "0") {
            return "1";
        }

        if (is_numeric($existingValue) && is_string($existingValue)) {
            return $existingValue + 5;
        }

        if ($existingValue === '' && stripos($iniName, 'callback') !== false) {
            return 'strlen';
        }

        return null;
    }

    private static function getLastErrorMessage() {
        return self::$lastError;
    }
}
