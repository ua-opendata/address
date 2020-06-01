<?php

namespace Horat1us\Houses\CLI\Data;

class Provider
{
    private const CSV_FILENAME = 'houses.csv';
    private const CSV_URL = 'http://services.ukrposhta.com/postindex_new/upload/houses.zip';
    private Path $path;

    public function __construct(Path $path)
    {
        $this->path = $path;
        if (!file_exists($this->path->get(static::CSV_FILENAME))) {
            $this->download();
        }
    }

    public function headers(): array
    {
        $resource = fopen($this->path->get(static::CSV_FILENAME), 'r');
        $headers = fgetcsv($resource, 0, ';');
        fclose($resource);
        return $headers;
    }

    public function length(): int
    {
        $length = 0;
        $resource = fopen($this->path->get(static::CSV_FILENAME), 'r');
        while (!feof($resource)) {
            fgets($resource);
            $length++;
        }
        return $length;
    }

    public function raw(): \Generator
    {
        $resource = fopen($this->path->get(static::CSV_FILENAME), 'r');
        fgets($resource);
        while (($line = fgetcsv($resource, 0, ';')) !== false) {
            unset($line[6]);
            foreach ($line as &$element) {
                $element = trim($element);
            }
            $line[5] = array_map(
                fn($el) => is_numeric($el) ? intval($el) : $el,
                array_filter(explode(",", $line[5])),
            );
            yield $line;
        }
    }

    private function download(): void
    {
        $contents = file_get_contents(static::CSV_URL);
        if ($contents === false) {
            throw new \RuntimeException("Unable to download " . static::CSV_URL);
        }
        $zipPath = $this->path->get(basename(parse_url(static::CSV_URL, PHP_URL_PATH)));
        file_put_contents(
            $zipPath,
            $contents
        );
        $archive = new \ZipArchive();
        if (!$archive->open($zipPath)) {
            @unlink($zipPath);
            throw new \RuntimeException("Unable to open ZIP " . $zipPath);
        }
        $stream = $archive->getStream('houses.csv');
        $file = fopen($this->path->get(static::CSV_FILENAME), 'w');
        while (($line = fgets($stream)) !== false) {
            fwrite(
                $file,
                mb_convert_encoding($line, 'UTF-8', 'Windows-1251')
            );
        }
        fclose($file);
        $archive->close();
        unlink($zipPath);
    }
}
