<?php

namespace PHPWatch\SymbolData;

use RuntimeException;

class Output {
    private $data = [];

    private $dir;

    private $flattedExport = [];

    public function __construct(string $dir = 'scratch') {
        $this->dir = $dir;
    }

    public function addData(string $key, $data, bool $flattedExport = false): void {
        $this->data[$key] = $data;
        if ($flattedExport) {
            $this->flattedExport[$key] = true;
        }
    }

    public function write(): void {
        if (!is_dir($this->dir) && !mkdir($concurrentDirectory = $this->dir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $dirCache = [];

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
                $this->removeArrayIndexes($data);
            }

            $data = "<?php\n\nreturn " . $data . ";\n";
            file_put_contents($filename, $data);
        }
    }

    private function removeArrayIndexes(string $output): string {

    }
}
