<?php

namespace GenDiff\Formatters\Plain;

use Funct\Collection;
use Funct\Strings;

function getValueMap($value)
{
    return (is_object($value) || is_array($value))
            ? 'complex value' : Strings\strip(json_encode($value), '"');
}

function getRawData($nodes, $pathToKey = "")
{
    $data = array_map(
        function ($key, $node) use ($pathToKey) {
            switch ($node->type) {
                case 'changed':
                    $oldValue = getValueMap($node->oldValue);
                    $newValue = getValueMap($node->newValue);
                    $raw = "Property '{$pathToKey}{$key}' was changed. From '{$oldValue}' to '{$newValue}'";
                    return $raw;
                case 'deleted':
                    $raw = "Property '{$pathToKey}{$key}' was removed";
                    return $raw;
                case 'added':
                    $value = getValueMap($node->newValue);
                    $raw = "Property '{$pathToKey}{$key}' was added with value: '$value'";
                    return $raw;
                case 'nested':
                    return getRawData($node->children, "{$pathToKey}{$key}.");
            }
        },
        array_keys($nodes),
        $nodes
    );
    return Collection\compact($data);
}

function render($diffAst)
{
    $rawData = getRawData($diffAst);
    return implode("\n", Collection\flattenAll($rawData)) . PHP_EOL;
}
