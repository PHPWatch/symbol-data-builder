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

require __DIR__ . '/../vendor/autoload.php';

$PHPWatchSymbols = array(
    'ext' => get_loaded_extensions(),
    'const' => get_defined_constants(true),
    'class' => get_declared_classes(),
    'trait' => PHP_VERSION_ID >= 50400 ? get_declared_traits() : null,
    'interface' => get_declared_interfaces(),
    'function' => FunctionsListSource::getData(),
    'ini' => ini_get_all(),
    'attribute' => AttributesListSource::getData(),
    'phpinfo' => PHPInfoSource::getData(),
);

$dumper = new Dumper(new Output());

$dumper->addSource(new ExtensionListSource($PHPWatchSymbols['ext']));
$dumper->addSource(new ConstantsSource($PHPWatchSymbols['const']));
$dumper->addSource(new ClassesListSource($PHPWatchSymbols['class']));
$dumper->addSource(new TraitsListSource(isset($PHPWatchSymbols['trait']) ? $PHPWatchSymbols['trait'] : array()));
$dumper->addSource(new InterfacesListSource($PHPWatchSymbols['interface']));
$dumper->addSource(new FunctionsListSource($PHPWatchSymbols['function']));
$dumper->addSource(new INIListSource($PHPWatchSymbols['ini']));
$dumper->addSource(new AttributesListSource($PHPWatchSymbols['attribute']));
$dumper->addSource(new PHPInfoSource($PHPWatchSymbols['phpinfo']));

$dumper->dump();
