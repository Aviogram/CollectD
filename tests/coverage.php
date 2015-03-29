<?php
$inputFile = $argv[1];

if (!file_exists($inputFile)) {
    throw new InvalidArgumentException('Invalid input file provided');
}

$xml = new SimpleXMLElement(file_get_contents($inputFile));
/* @var $metrics SimpleXMLElement[] */
$metrics = $xml->xpath('//metrics');

$totalElements = 0;
$checkedElements = 0;

foreach ($metrics as $metric) {
    $totalElements   += (int) $metric['elements'];
    $checkedElements += (int) $metric['coveredelements'];
}

$coverage = round(($checkedElements / $totalElements) * 100, 2);
echo "{$coverage}% covered\n\n";
