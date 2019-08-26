<?php

namespace GenDiff\Formatters\Json;

use Funct\Collection;
use Funct\Strings;

function render($diffAst)
{
    return json_encode($diffAst, JSON_PRETTY_PRINT) . PHP_EOL;
}
