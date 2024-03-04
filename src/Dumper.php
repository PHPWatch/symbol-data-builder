<?php

namespace PHPWatch\SymbolData;

class Dumper {
    /**
     * @var DataSource[]
     */
    private $sources = [];

    /**
     * @var Output
     */
    private $output;

    public function __construct(Output $output) {
        $this->output = $output;
    }

    public function addSource(DataSource $source): void {
        $this->sources[] = $source;
    }

    public function dump(): void {
        $this->enumerate();
        $this->output->write();
    }

    private function enumerate(): void {
        foreach ($this->sources as $source) {
            $source->addDataToOutput($this->output);
        }
    }
}
