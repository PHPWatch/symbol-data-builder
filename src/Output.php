<?php

namespace PHPWatch\SymbolData;

use RuntimeException;

class Output {
    private $data = [];

    private $dir;

    public function __construct(string $dir = 'scratch') {
        $this->dir = $dir;
    }

    public function addData(string $key, $data): void {
        $this->data[$key] = $data;
    }

    public function write(): void {
        if (!is_dir($this->dir) && !mkdir($concurrentDirectory = $this->dir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        foreach ($this->data as $key => $data) {
            $filename = $this->dir . '/' . $key . '.php';

            if (!is_dir(dirname($filename))) {
                mkdir(dirname($filename), 0777, true);
            }

            $data = var_export($data, true);
            $data = "<?php\n\nreturn " . $data . ";\n";
            file_put_contents($filename, $data);
        }
    }
}
