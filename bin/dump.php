<?php

use PHPWatch\SymbolData\ConstantsSource;
use PHPWatch\SymbolData\Output;

require __DIR__ . '/../vendor/autoload.php';

$output = new Output((int) (floor(PHP_VERSION_ID / 100) * 100));

$output->addData('constants', ConstantsSource::getAllData());
