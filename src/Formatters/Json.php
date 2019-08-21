<?php

namespace GenDiff\Formatters\Json;

use Funct\Collection;
use Funct\Strings;

function getRawData($ast)
{
    return array_reduce(
        array_keys($ast),
        function ($data, $attr) use ($ast) {
            switch ($ast[$attr]->type) {
                case 'changed':
                    $data[$attr] = Collection\compact((array) $ast[$attr]);
                    break;
                case 'deleted':
                    $data[$attr] = Collection\compact((array) $ast[$attr]);
                    break;
                case 'added':
                    $data[$attr] = Collection\compact((array) $ast[$attr]);
                    break;
                case 'nested':
                    $data[$attr] = getRawData($ast[$attr]->children);
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
    return json_encode($rawData, JSON_PRETTY_PRINT) . PHP_EOL;
}
