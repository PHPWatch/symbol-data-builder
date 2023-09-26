<?php

namespace PHPWatch\SymbolData;

$PHPWatchSymbols = [
    'ext' => get_loaded_extensions(),
    'const' => get_defined_constants(true),
    'class' => get_declared_classes(),
    'trait' => get_declared_traits(),
    'interface' => get_declared_interfaces(),
    'function' => get_defined_functions()['internal'],
    'ini' => ini_get_all(),
    'attribute' => [],
    'phpinfo' => (function(): string {
        ob_start();
        // Do not include env of build info as they change in every build and run
        phpinfo(INFO_CREDITS|INFO_LICENSE|INFO_MODULES|INFO_CONFIGURATION);
        return ob_get_clean();
    })(),
];

require __DIR__ . '/../vendor/autoload.php';

$output = new Output();

$output->addData(ExtensionListSource::NAME, $PHPWatchSymbols['ext']);
$output->addData(ConstantsSource::NAME, $PHPWatchSymbols['const']);
$output->addData(ClassesListSource::NAME, $PHPWatchSymbols['class']);
$output->addData(TraitsListSource::NAME, $PHPWatchSymbols['trait']);
$output->addData(InterfacesListSource::NAME, $PHPWatchSymbols['interface']);
$output->addData(FunctionsListSource::NAME, $PHPWatchSymbols['function']);
$output->addData(INIListSource::NAME, $PHPWatchSymbols['ini']);
$output->addData(AttributesListSource::NAME, AttributesListSource::getAllData());
$output->addData(PHPInfoSource::NAME, PHPInfoSource::getAllData());

ExtensionListSource::handleExtensionList($PHPWatchSymbols['ext'], $output);
ClassesListSource::handleClassList($PHPWatchSymbols['class'], $output);
InterfacesListSource::handleInterfaceList($PHPWatchSymbols['interface'], $output);

$output->write();
