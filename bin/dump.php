<?php

namespace PHPWatch\SymbolData;

require __DIR__ . '/../vendor/autoload.php';

$output = new Output();

$output->addData(ExtensionListSource::NAME, ExtensionListSource::getAllData());
$output->addData(ConstantsSource::NAME, ConstantsSource::getAllData());
$output->addData(ClassesListSource::NAME, ClassesListSource::getAllData());
$output->addData(TraitsListSource::NAME, TraitsListSource::getAllData());
$output->addData(InterfacesListSource::NAME, InterfacesListSource::getAllData());
$output->addData(FunctionsListSource::NAME, FunctionsListSource::getAllData());
$output->addData(INIListSource::NAME, INIListSource::getAllData());
$output->addData(PHPInfoSource::NAME, PHPInfoSource::getAllData());

$output->write();
