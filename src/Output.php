<?php

namespace PHPWatch\SymbolData;

use RuntimeException;

class Output {
    private $data = array();

    private $dir;

    private $flattedExport = array();

    public function __construct($dir = 'scratch') {
        $this->dir = $dir;
    }

    public function addData($key, $data, $flattedExport = false) {
        $this->data[$key] = $data;
        if ($flattedExport) {
            $this->flattedExport[$key] = true;
        }
    }

    public function write() {
        if (!is_dir($this->dir) && !mkdir($concurrentDirectory = $this->dir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $dirCache = array();

        foreach ($this->data as $key => $data) {
            $filename = $this->dir . '/' . $key . '.php';
            $dir = dirname($filename);

            if (empty($dirCache[$dir])) {
                if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }

                $dirCache[dirname($filename)] = true;
            }

            $data = var_export($data, true);

            if (!empty($this->flattedExport[$key])) {
                $data = $this->postProcessFlatArray($data);
            }

            $data = "<?php\n\nreturn " . $data . ";\n";
            file_put_contents($filename, $data);
        }

    }

    private function postProcessFlatArray($export) {
        return preg_replace('/^(?<indent>\s\s)(?<rm>(?:\d+)\s=>\s)(?<line>.*),$/m', '$1$3,', $export);
    }
}
