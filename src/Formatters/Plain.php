<?php

namespace GenDiff\Formatters\Plain;

use Funct\Collection;
use Funct\Strings;

function getValueMap($value)
{
    return (is_object($value) || is_array($value))
            ? 'complex value' : Strings\strip(json_encode($value), '"');
}

function getRawData($ast, $property = "")
{
    $data = array_map(
        function ($attr, $node) use ($property) {
            switch ($node->type) {
                case 'changed':
                    $property .= $attr;
                    $oldValue = getValueMap($node->oldValue);
                    $newValue = getValueMap($node->newValue);
                    $raw = "Property '{$property}' was changed. From '{$oldValue}' to '{$newValue}'";
                    return $raw;
                case 'deleted':
                    $property .= $attr;
                    $raw = "Property '{$property}' was removed";
                    return $raw;
                case 'added':
                    $property .= $attr;
                    $value = getValueMap($node->newValue);
                    $raw = "Property '{$property}' was added with value: '$value'";
                    return $raw;
                case 'nested':
                    $property .= "{$attr}.";
                    return getRawData($node->children, $property);
            }
        },
        array_keys($ast),
        $ast
    );
    return Collection\compact($data);
}

function render($diffAst)
{
    $rawData = getRawData($diffAst);
    return implode("\n", Collection\flattenAll($rawData)) . PHP_EOL;
}
