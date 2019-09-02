<?php

namespace GenDiff\Differ;

use function GenDiff\Parsers\parse;
use function GenDiff\Renderers\render;
use Funct\Collection;

const NODE_TYPES = ['deleted', 'added', 'nested', 'unchanged', 'changed'];
const CHECK_TYPE_FUNCTIONS = ['isDeleted', 'isAdded', 'isNested', 'isNotChanged', 'isChanged'];

function compareValues($value1, $value2)
{
    if (is_array($value1) && is_array($value2)) {
        return empty(array_diff($value1, $value2));
    }
    return ($value1 === $value2);
}

function isDeleted($data1, $data2, $key)
{
    return (property_exists($data1, $key) && !property_exists($data2, $key));
}

function isAdded($data1, $data2, $key)
{
    return (!property_exists($data1, $key) && property_exists($data2, $key));
}

function isNested($data1, $data2, $key)
{
    $keyExistStatus = property_exists($data1, $key) && property_exists($data2, $key);
    return $keyExistStatus ? (is_object($data1->$key) && is_object($data2->$key)) : false;
}

function isNotChanged($data1, $data2, $key)
{
    $keyExistStatus = property_exists($data1, $key) && property_exists($data2, $key);
    return $keyExistStatus ? compareValues($data1->$key, $data2->$key) : false;
}

function isChanged($data1, $data2, $key)
{
    $keyExistStatus = property_exists($data1, $key) && property_exists($data2, $key);
    return $keyExistStatus ? !compareValues($data1->$key, $data2->$key) : false;
}

function getNodeType($data1, $data2, $key)
{
    $typesMapping = array_map(
        function ($type, $checkFunc) {
            return compact('type', 'checkFunc');
        },
        NODE_TYPES,
        CHECK_TYPE_FUNCTIONS
    );
    $checkTypeFunc = Collection\first(
        array_filter(
            CHECK_TYPE_FUNCTIONS,
            function ($fn) use ($data1, $data2, $key) {
                $fullFuncName = __NAMESPACE__ . "\\" . $fn;
                return $fullFuncName($data1, $data2, $key);
            }
        )
    );
    $typeMap = Collection\findWhere($typesMapping, ['checkFunc' => $checkTypeFunc]);
    return $typeMap['type'];
}

function getNodeValue($type, $data1, $data2, $key)
{
    $valuesMapping = [
        'deleted' => function ($data1, $data2, $key) {
            return $data1->$key;
        },
        'added' => function ($data1, $data2, $key) {
            return $data2->$key;
        },
        'unchanged' => function ($data1, $data2, $key) {
            return $data1->$key;
        },
        'changed' => function ($data1, $data2, $key) {
            return ['old' => $data1->$key, 'new' => $data2->$key];
        },
    ];
    return $valuesMapping[$type]($data1, $data2, $key);
}

function buildDiffAst($data1, $data2)
{
    $uniqueKeys = Collection\Union(
        array_keys((array) $data1),
        array_keys((array) $data2)
    );
    return array_reduce(
        $uniqueKeys,
        function ($ast, $key) use ($data1, $data2) {
            $ast[$key] = new \stdClass();
            $ast[$key]->type = getNodeType($data1, $data2, $key);
            if ($ast[$key]->type === 'nested') {
                $ast[$key]->children = buildDiffAst($data1->$key, $data2->$key);
            } else {
                $ast[$key]->value = getNodeValue($ast[$key]->type, $data1, $data2, $key);
            }
            return $ast;
        },
        []
    );
}

function genDiff($pathToFile1, $pathToFile2, $format = "pretty")
{
    if (!file_exists($pathToFile1)) {
        throw new \Exception("Файл {$pathToFile1} не найден!");
    }
    if (!file_exists($pathToFile2)) {
        throw new \Exception("Файл {$pathToFile2} не найден!");
    }
    if (!in_array($format, ["pretty", "plain", "json"])) {
        throw new \Exception("Задан неизвестный формат отображения: '{$format}'!");
    }

    $data1 = file_get_contents($pathToFile1);
    $fileExtension1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $config1 = parse($data1, $fileExtension1);

    $data2 = file_get_contents($pathToFile2);
    $fileExtension2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);
    $config2 = parse($data2, $fileExtension2);

    $diffAst = buildDiffAst($config1, $config2);
    return render($diffAst, $format);
}
