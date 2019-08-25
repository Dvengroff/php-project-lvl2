<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $type)
{
    $mapping = [
        'json' => function ($rawData) {
            return json_decode($rawData);
        },
        'yml' => function ($rawData) {
            return Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP);
        }
    ];

    return $mapping[$type]($data);
}
