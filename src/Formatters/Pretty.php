<?php

namespace GenDiff\Formatters\Pretty;

use Funct\Collection;
use Funct\Strings;

function getRawData($ast)
{
    return array_reduce(
        array_keys($ast),
        function ($data, $attr) use ($ast) {
            switch ($ast[$attr]->type) {
                case 'unchanged':
                    $key = "{$attr}";
                    $data[$key] = $ast[$attr]->newValue;
                    break;
                case 'changed':
                    $oldKey = "- {$attr}";
                    $data[$oldKey] = $ast[$attr]->oldValue;
                    $newKey = "+ {$attr}";
                    $data[$newKey] = $ast[$attr]->newValue;
                    break;
                case 'deleted':
                    $key = "- {$attr}";
                    $data[$key] = $ast[$attr]->oldValue;
                    break;
                case 'added':
                    $key = "+ {$attr}";
                    $data[$key] = $ast[$attr]->newValue;
                    break;
                case 'nested':
                    $key = "{$attr}";
                    $data[$key] = getRawData($ast[$attr]->children);
                    break;
            }
            return $data;
        },
        []
    );
}

function render($diffAst)
{
    $rawData = getRawData($diffAst);
    $rawDataString = json_encode($rawData, JSON_PRETTY_PRINT);
    $rawDataArr = explode("\n", $rawDataString);
    $formattedDataArr = array_map(
        function ($rawString) {
            $formattedString = Strings\strip($rawString, '"', ",");
            $firstChar = trim($formattedString)[0];
            return (($firstChar === "+") || ($firstChar === "-"))
                    ? Strings\chompLeft($formattedString, "  ") : $formattedString;
        },
        $rawDataArr
    );

    return implode("\n", $formattedDataArr) . PHP_EOL;
}
