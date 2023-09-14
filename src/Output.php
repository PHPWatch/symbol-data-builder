<?php

namespace PHPWatch\SymbolData;
class Output {
    private $outputDir;
    public function __construct(string $outputDir) {
        $this->outputDir = $outputDir;
    }

    public function addData(string $key, $data): void {

    }
}
