<?php

use PHPWatch\SymbolData\ConstantsSource;
use PHPWatch\SymbolData\ExtensionListSource;
use PHPWatch\SymbolData\Output;

require __DIR__ . '/../vendor/autoload.php';

$output = new Output();

$output->addData(ConstantsSource::NAME, ConstantsSource::getAllData());
$output->addData(ExtensionListSource::NAME, ExtensionListSource::getAllData());

$output->write();
