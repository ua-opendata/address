<?php
require_once("./vendor/autoload.php");

use Horat1us\Houses\CLI\Data;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\ProgressBar;

$path = new Data\Path();
$dataset = new Data\Provider($path);
$slugger = new Data\Slugger();

$data = [];

$output = new ConsoleOutput();
$progressBar = new ProgressBar($output, $dataset->length());
$progressBar->start();

foreach ($dataset->raw() as $i => [$region, $district, $locality, $code, $street, $numbers]) {
    $data[$region] ??= [];
    $data[$region][$locality] ??= [];
    $data[$region][$locality][$street] ??= [];
    array_push($data[$region][$locality][$street], ...$numbers);

    $progressBar->advance();
}
$progressBar->finish();

$path->writeJson('houses.json', $data);

$regions = [];
foreach ($data as $name => $localities) {
    $slug = mb_strtolower($slugger->slug($name, ''));
    $file = "regions/$slug.json";
    $regions[] = compact('name', 'file');
}
$path->writeJson('regions.json', $regions);

foreach ($regions as ['name' => $name, 'file' => $file]) {
    $localities = [];
    $dir = str_replace('.json', '/', $file);
    foreach ($data[$name] as $locality => $streets) {
        [$localityType, $localityName] = preg_split('/\s/u', $locality, 2);
        $localitySlug = $slugger->slug($locality);
        $localityFile = $dir . $localitySlug . '.json';

        $streetsList = [];
        foreach ($streets as $street => $houses) {
            if (!is_string($street)) {
                print_r($streets);
                continue;
            }
            $streetComponents = preg_split('/\s/u', $street, 2);
            [$streetType, $streetName] = $streetComponents;
            $streetsList[] = [
                'name' => $streetName,
                'type' => $streetType,
                'houses' => $houses,
            ];
        }
        $localities[] = [
            'name' => $localityName,
            'type' => $localityType,
            'file' => $localityFile,
        ];
        $path->writeJson($localityFile, $streetsList);
    }
    $path->writeJson($file, $localities);
}
