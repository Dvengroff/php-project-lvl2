<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($pathToFile)
{
    $rawData = file_get_contents($pathToFile);
    $fileExtension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($fileExtension === "json") {
        return json_decode($rawData);
    } elseif ($fileExtension === "yml") {
        return Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP);
    }
}
