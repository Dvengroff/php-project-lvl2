<?php

namespace GenDiff\Formatters\Plain;

use Funct\Collection;
use Funct\Strings;

function getValueMap($value)
{
    return (is_object($value) || is_array($value))
            ? 'complex value' : Strings\strip(json_encode($value), '"');
}

function getPlainRaw($type, $property, $value)
{
    $mapping = [
        'deleted' => function ($property, $value) {
            return "Property '{$property}' was removed";
        },
        'added' => function ($property, $value) {
            $newValue = getValueMap($value);
            return "Property '{$property}' was added with value: '$newValue'";
        },
        'unchanged' => function ($property, $value) {
            return "";
        },
        'changed' => function ($property, $value) {
            $oldValue = getValueMap($value['old']);
            $newValue = getValueMap($value['new']);
            return "Property '{$property}' was changed. From '{$oldValue}' to '{$newValue}'";
        },
    ];
    return $mapping[$type]($property, $value);
}

function getDataMap($nodes, $pathToKey = "")
{
    $data = array_map(
        function ($key, $node) use ($pathToKey) {
            if ($node->type === 'nested') {
                return getDataMap($node->children, "{$pathToKey}{$key}.");
            } else {
                return getPlainRaw($node->type, "{$pathToKey}{$key}", $node->value);
            }
        },
        array_keys($nodes),
        $nodes
    );
    return Collection\compact($data);
}

function render($diffAst)
{
    $dataMap = getDataMap($diffAst);
    return implode("\n", Collection\flattenAll($dataMap)) . PHP_EOL;
}
