<?php

namespace GenDiff\Renderers;

function render($differAst, $config1, $config2)
{
    $differArr = array_reduce(
        array_keys($differAst),
        function ($acc, $key) use ($differAst, $config1, $config2) {
            switch ($differAst[$key]) {
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
