<?php

namespace GenDiff\Formatters\Json;

use Funct\Collection;
use Funct\Strings;

function render($diffAst)
{
    return json_encode($diffAst, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
