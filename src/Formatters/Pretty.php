<?php

namespace GenDiff\Formatters\Pretty;

use Funct\Collection;
use Funct\Strings;

const DEFAULT_INDENT = 4;

function getValueMap($value, $depth)
{
    if (is_object($value)) {
        $valueMapArr = array_map(
            function ($key, $val) use ($depth) {
                $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 2));
                return "{$indent}{$key}: {$val}";
            },
            array_keys((array) $value),
            (array) $value
        );
        $valueMapString = implode("\n", $valueMapArr);
        $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
        return "{\n{$valueMapString}\n{$indent}}";
    } elseif (is_array($value)) {
        $valueMapArr = array_map(
            function ($item) use ($depth) {
                $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 2));
                return "{$indent}{$item}";
            },
            $value
        );
        $valueMapString = implode("\n", $valueMapArr);
        $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));
        return "[\n{$valueMapString}\n{$indent}]";
    } else {
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
