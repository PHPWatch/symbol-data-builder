<?php

namespace PHPWatch\SymbolData;

interface DataSource
{
    public function addDataToOutput(Output $output): void;
}
