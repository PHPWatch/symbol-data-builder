<?php

namespace PHPWatch\SymbolData;

use PHPWatch\SymbolData\Sources\AttributesListSource;
use PHPWatch\SymbolData\Sources\ClassesListSource;
use PHPWatch\SymbolData\Sources\ConstantsSource;
use PHPWatch\SymbolData\Sources\ExtensionListSource;
use PHPWatch\SymbolData\Sources\FunctionsListSource;
use PHPWatch\SymbolData\Sources\INIListSource;
use PHPWatch\SymbolData\Sources\InterfacesListSource;
use PHPWatch\SymbolData\Sources\PHPInfoSource;
use PHPWatch\SymbolData\Sources\TraitsListSource;

function phpwatch_get_declared_functions() {
    /** @noinspection PotentialMalwareInspection */
    $funcs = get_defined_functions();
    return $funcs['internal'];
}

function phpwatch_get_declared_attributes() {
    $data = array();

    if (PHP_VERSION_ID < 80000) {
        return array();
    }

    if (!class_exists('Attribute', false)) {
        return $data;
    }

    foreach (get_declared_classes() as $name) {
        $reflection = new \ReflectionClass($name);

        if ($reflection->getAttributes('Attribute') !== array()) {
            $data[] = $reflection->getName();
        }
    }

    return $data;
}

function phpwatch_get_phpinfo() {
    ob_start();
    // Do not include env of build info as they change in every build and run
    phpinfo(INFO_CREDITS|INFO_LICENSE|INFO_MODULES|INFO_CONFIGURATION);
    return ob_get_clean();
}

$PHPWatchSymbols = array(
    'ext' => get_loaded_extensions(),
    'const' => get_defined_constants(true),
    'class' => get_declared_classes(),
    'trait' => PHP_VERSION_ID >= 50400 ? get_declared_traits() : array(),
    'interface' => get_declared_interfaces(),
    'function' => phpwatch_get_declared_functions(),
    'ini' => ini_get_all(),
    'attribute' => phpwatch_get_declared_attributes(),
    'phpinfo' => phpwatch_get_phpinfo(),
);

require __DIR__ . '/../preload.php';

$dumper = new Dumper(new Output());

$dumper->addSource(new ExtensionListSource($PHPWatchSymbols['ext']));
$dumper->addSource(new ConstantsSource($PHPWatchSymbols['const']));
$dumper->addSource(new ClassesListSource($PHPWatchSymbols['class']));
$dumper->addSource(new TraitsListSource($PHPWatchSymbols['trait']));
$dumper->addSource(new InterfacesListSource($PHPWatchSymbols['interface']));
$dumper->addSource(new FunctionsListSource($PHPWatchSymbols['function']));
$dumper->addSource(new INIListSource($PHPWatchSymbols['ini']));
$dumper->addSource(new AttributesListSource($PHPWatchSymbols['attribute']));
$dumper->addSource(new PHPInfoSource($PHPWatchSymbols['phpinfo']));

$dumper->dump();
