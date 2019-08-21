<?php

namespace GenDiff\Differ;

use function GenDiff\Parsers\parse;
use function GenDiff\Renderers\render;
use Funct\Collection;

function compareValues($value1, $value2)
{
    if (is_array($value1) && is_array($value2)) {
        return empty(array_diff($value1, $value2));
    }
    return ($value1 === $value2);
}

function buildDiffAst($data1, $data2)
{
    $uniqueKeys = Collection\Union(
        array_keys((array) $data1),
        array_keys((array) $data2)
    );
    
    return array_reduce(
        $uniqueKeys,
        function ($astNodes, $key) use ($data1, $data2) {
            $astNodes[$key] = new \stdClass();
            $astNodes[$key]->children = null;
            $astNodes[$key]->oldValue = null;
            $astNodes[$key]->newValue = null;
            if (property_exists($data1, $key) && !property_exists($data2, $key)) {
                $astNodes[$key]->type = 'deleted';
                $astNodes[$key]->oldValue = $data1->$key;
            } elseif (!property_exists($data1, $key) && property_exists($data2, $key)) {
                $astNodes[$key]->type = 'added';
                $astNodes[$key]->newValue = $data2->$key;
            } elseif (is_object($data1->$key) && is_object($data2->$key)) {
                $astNodes[$key]->type = 'nested';
                $astNodes[$key]->children = buildDiffAst($data1->$key, $data2->$key);
            } else {
                $astNodes[$key]->type = compareValues($data1->$key, $data2->$key)
                                ? 'unchanged' : 'changed';
                $astNodes[$key]->oldValue = $data1->$key;
                $astNodes[$key]->newValue = $data2->$key;
            }
            return $astNodes;
        },
        []
    );
}

function genDiff($pathToFile1, $pathToFile2)
{
    if (!file_exists($pathToFile1)) {
        throw new \Exception("Файл {$pathToFile1} не найден!");
    }
    if (!file_exists($pathToFile2)) {
        throw new \Exception("Файл {$pathToFile2} не найден!");
    }

    $config1 = parse($pathToFile1);
    $config2 = parse($pathToFile2);
    
    $diffAst = buildDiffAst($config1, $config2);
 
    return render($diffAst);
}
