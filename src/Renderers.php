<?php

namespace GenDiff\Renderers;

use GenDiff\Formatters;

function render($diffAst, $format)
{
    $mapping = [
        'pretty' => Formatters\Pretty\render($diffAst),
        'plain' => Formatters\Plain\render($diffAst),
        'json' => Formatters\Json\render($diffAst)
    ];

    return $mapping[$format];
}
