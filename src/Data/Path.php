<?php

namespace Horat1us\Houses\CLI\Data;

class Path
{
    private string $root;

    public function __construct()
    {
        $rootPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'docs';
        $this->validateDir($rootPath);
        $this->root = $rootPath;
    }

    public function root(): string
    {
        return $this->root;
    }

    public function get(string $fileName): string
    {
        return $this->root . DIRECTORY_SEPARATOR . $fileName;
    }

    public function writeJson(string $fileName, $data): void
    {
        $path = $this->get($fileName);
        if (dirname($path) !== $this->root) {
            $this->validateDir(dirname($path));
        }
        file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function validateDir($path): void
    {
        if (!(is_dir($path) || mkdir($path))) {
            throw new \RuntimeException("Unable to create $path");
        }
    }
}
