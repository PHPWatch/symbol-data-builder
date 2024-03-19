<?php

namespace Tests\PHPWatch\SymbolData;

use PHPWatch\SymbolData\DataSource;
use PHPWatch\SymbolData\Dumper;
use PHPWatch\SymbolData\Output;

class DumperTest extends TestCase {
    /**
     * Tests that calling Dumper::dump() calls the sources and output.
     *
     * @covers Dumper
     *
     * @return void
     */
    public function testDumpCallsOutputWrite() {
        $output = $this->createMock(Output::class);
        $output->expects($this->once())->method('write');

        $source = $this->createMock(DataSource::class);
        $source->expects($this->atLeastOnce())->method('addDataToOutput');

        $dumper = new Dumper($output);
        $dumper->addSource($source);

        $dumper->dump();
    }
}
