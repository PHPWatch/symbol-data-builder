<?php

namespace PHPWatch\SymbolData;
class Output {
    private $outputDir;
    private $data = [];

    private $dir;
    public function __construct(string $outputDir, string $dir = 'scratch') {
        $this->outputDir = $outputDir;
        $this->dir = $dir;
    }
    public function addData(string $key, $data): void {
        $this->data[$key] = $data;
    }

    public function write(): void {
        if (!is_dir($this->dir) && !mkdir($concurrentDirectory = $this->dir) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!is_dir($this->dir . '/' . $this->outputDir) && !mkdir(
                $concurrentDirectory = $this->dir . '/' . $this->outputDir
            ) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

        foreach ($this->data as $key => $data) {
            $data = var_export($data, true);
            file_put_contents($this->dir . '/' . $this->outputDir . '/' . $key . '.json', $data);
        }
    }
}
