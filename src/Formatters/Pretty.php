<?php

namespace GenDiff\Formatters\Pretty;

use Funct\Collection;
use Funct\Strings;

const DEFAULT_INDENT = 4;

function getObjectMap($data, $depth)
{
    $dataMapArr = array_map(
        function ($key, $value) use ($depth) {
            $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 2));
            return "{$indent}{$key}: {$value}";
        },
        array_keys((array) $data),
        (array) $data
    );
    $dataMapString = implode("\n", $dataMapArr);
    $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
    return "{\n{$dataMapString}\n{$indent}}";
}

function getArrayMap($data, $depth)
{
    $dataMapArr = array_map(
        function ($value) use ($depth) {
            $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 2));
            return "{$indent}{$value}";
        },
        $data
    );
    $dataMapString = implode("\n", $dataMapArr);
    $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
    return "[\n{$dataMapString}\n{$indent}]";
}

function getValueMap($value, $depth)
{
    switch (gettype($value)) {
        case 'object':
            return getObjectMap($value, $depth);
        case 'array':
            return getArrayMap($value, $depth);
        default:
            return Strings\strip(json_encode($value), '"');
    }
}

function getDataMap($nodes, $depth = 0)
{
    $rawData = array_map(
        function ($key, $node) use ($depth) {
            $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
            switch ($node->type) {
                case 'unchanged':
                    $value = getValueMap($node->oldValue, $depth);
                    return "{$indent}{$key}: {$value}";
                case 'changed':
                    $oldValue = getValueMap($node->oldValue, $depth);
                    $oldRaw = "{$indent}- {$key}: {$oldValue}";
                    $newValue = getValueMap($node->newValue, $depth);
                    $newRaw = "{$indent}+ {$key}: {$newValue}";
                    return [Strings\chompLeft($oldRaw, "  "), Strings\chompLeft($newRaw, "  ")];
                case 'deleted':
                    $value = getValueMap($node->oldValue, $depth);
                    $raw = "{$indent}- {$key}: {$value}";
                    return Strings\chompLeft($raw, "  ");
                case 'added':
                    $value = getValueMap($node->newValue, $depth);
                    $raw = "{$indent}+ {$key}: {$value}";
                    return Strings\chompLeft($raw, "  ");
                case 'nested':
                    $value = getDataMap($node->children, $depth + 1);
                    return "{$indent}{$key}: {$value}";
            }
        },
        array_keys($nodes),
        $nodes
    );

    $rawDataString = implode("\n", Collection\flattenAll($rawData));
    $indent = str_repeat(" ", DEFAULT_INDENT * $depth);
    return "{\n{$rawDataString}\n{$indent}}";
}

function render($diffAst)
{
    return getDataMap($diffAst) . PHP_EOL;
}
