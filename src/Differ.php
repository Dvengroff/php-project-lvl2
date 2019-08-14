<?php

namespace GenDiff\Differ;

function genDiff($pathToFile1, $pathToFile2)
{
    if (!file_exists($pathToFile1)) {
        throw new \Exception("Файл {$pathToFile1} не найден!");
    }
    if (!file_exists($pathToFile2)) {
        throw new \Exception("Файл {$pathToFile2} не найден!");
    }

    $rawData1 = file_get_contents($pathToFile1);
    $config1 = array_map(
        function ($value) {
            return (is_bool($value)) ? var_export($value, true) : $value;
        },
        json_decode($rawData1, true)
    );
    $rawData2 = file_get_contents($pathToFile2);
    $config2 = array_map(
        function ($value) {
            return (is_bool($value)) ? var_export($value, true) : $value;
        },
        json_decode($rawData2, true)
    );
    
    $allKeys = array_merge(array_keys($config1), array_keys($config2));
    $allUniqueKeys = array_unique($allKeys);

    $keyStateArr = array_reduce(
        $allUniqueKeys,
        function ($acc, $key) use ($config1, $config2) {
            if (!array_key_exists($key, $config2)) {
                $acc[$key] = 'deleted';
            } elseif (!array_key_exists($key, $config1)) {
                $acc[$key] = 'added';
            } else {
                $acc[$key] = ($config1[$key] === $config2[$key]) ? 'unchanged' : 'changed';
            }
            return $acc;
        },
        []
    );

    $differArr = array_reduce(
        $allUniqueKeys,
        function ($acc, $key) use ($keyStateArr, $config1, $config2) {
            switch ($keyStateArr[$key]) {
                case 'unchanged':
                    $acc[] = "  {$key}: {$config1[$key]}";
                    break;
                case 'changed':
                    $acc[] = "- {$key}: {$config1[$key]}";
                    $acc[] = "+ {$key}: {$config2[$key]}";
                    break;
                case 'deleted':
                    $acc[] = "- {$key}: {$config1[$key]}";
                    break;
                case 'added':
                    $acc[] = "+ {$key}: {$config2[$key]}";
                    break;
            }
            return $acc;
        },
        []
    );

    $differString = implode("\n  ", $differArr);
    return "{\n  " . $differString . "\n}" . PHP_EOL;
}