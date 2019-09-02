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

function getPrettyRaw($type, $key, $value, $depth)
{
    $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
    $mapping = [
        'deleted' => function ($key, $value, $depth) use ($indent) {
            $oldValue = getValueMap($value, $depth);
            $raw = "{$indent}- {$key}: {$oldValue}";
            return Strings\chompLeft($raw, "  ");
        },
        'added' => function ($key, $value, $depth) use ($indent) {
            $newValue = getValueMap($value, $depth);
            $raw = "{$indent}+ {$key}: {$newValue}";
            return Strings\chompLeft($raw, "  ");
        },
        'unchanged' => function ($key, $value, $depth) use ($indent) {
            $oldValue = getValueMap($value, $depth);
            return "{$indent}{$key}: {$oldValue}";
        },
        'changed' => function ($key, $value, $depth) use ($indent) {
            $oldValue = getValueMap($value['old'], $depth);
            $oldRaw = "{$indent}- {$key}: {$oldValue}";
            $newValue = getValueMap($value['new'], $depth);
            $newRaw = "{$indent}+ {$key}: {$newValue}";
            return [Strings\chompLeft($oldRaw, "  "), Strings\chompLeft($newRaw, "  ")];
        },
    ];
    return $mapping[$type]($key, $value, $depth);
}

function getDataMap($nodes, $depth = 0)
{
    $rawData = array_map(
        function ($key, $node) use ($depth) {
            if ($node->type === 'nested') {
                $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
                $value = getDataMap($node->children, $depth + 1);
                return "{$indent}{$key}: {$value}";
            } else {
                return getPrettyRaw($node->type, $key, $node->value, $depth);
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
