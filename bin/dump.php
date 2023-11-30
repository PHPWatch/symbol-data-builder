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
    'attribute' => (function(): array {
        $data = [];

        if (!class_exists(\Attribute::class)) {
            return $data;
        }

        foreach (get_declared_classes() as $name) {
            $reflection = new \ReflectionClass($name);

            if ($reflection->getAttributes(\Attribute::class) !== []) {
                $data[] = $reflection->getName();
            }
        }

        return $data;
    })(),
    'phpinfo' => (function(): string {
        ob_start();
        // Do not include env of build info as they change in every build and run
        phpinfo(INFO_CREDITS|INFO_LICENSE|INFO_MODULES|INFO_CONFIGURATION);
        return ob_get_clean();
    })(),
];

require __DIR__ . '/../vendor/autoload.php';

$output = new Output();

ExtensionListSource::handleExtensionList($PHPWatchSymbols['ext'], $output);
ConstantsSource::handleGroupedConstantList($PHPWatchSymbols['const'], $output);
ClassesListSource::handleClassList($PHPWatchSymbols['class'], $output);
TraitsListSource::handleTraitList($PHPWatchSymbols['trait'], $output);
InterfacesListSource::handleInterfaceList($PHPWatchSymbols['interface'], $output);
FunctionsListSource::handleFunctionList($PHPWatchSymbols['function'], $output);
INIListSource::handleIniList($PHPWatchSymbols['ini'], $output);
AttributesListSource::handleAttributeList($PHPWatchSymbols['attribute'], $output);
PHPInfoSource::handlePhpinfoString($PHPWatchSymbols['phpinfo'], $output);

$output->write();
