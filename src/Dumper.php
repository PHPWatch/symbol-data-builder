<?php

namespace PHPWatch\SymbolData;

class Dumper {
    /**
     * @var DataSource[]
     */
    private $sources = array();

    /**
     * @var Output
     */
    private $output;

    public function __construct(Output $output) {
        $this->output = $output;
    }

    public function addSource(DataSource $source) {
        $this->sources[] = $source;
    }

    public function dump() {
        $this->enumerate();
        $this->output->write();
    }

    private function enumerate() {
        foreach ($this->sources as $source) {
            $source->addDataToOutput($this->output);
        }
    }
}
