<?php

namespace GenDiff\Runner;

use function GenDiff\Differ\genDiff;

function run($doc)
{
    $args = \Docopt::handle($doc);

    try {
        $diff = genDiff($args['<firstFile>'], $args['<secondFile>']);
    } catch (\Exception $e) {
        exit($e->getMessage());
    }
    
    print_r($diff);
}
