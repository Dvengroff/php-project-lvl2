<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($pathToFile)
{
    $rawData = file_get_contents($pathToFile);
    $fileExtension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($fileExtension === "json") {
        return array_map(
            function ($value) {
                return (is_bool($value)) ? var_export($value, true) : $value;
            },
            json_decode($rawData, true)
        );
    } elseif ($fileExtension === "yml") {
        return array_map(
            function ($value) {
                return (is_bool($value)) ? var_export($value, true) : $value;
            },
            Yaml::parse($rawData)
        );
    }
}
