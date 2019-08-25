<?php

namespace GenDiff\Runner;

use function GenDiff\Differ\genDiff;

function run($doc)
{
    $args = \Docopt::handle($doc, ['version' => 'GenDiff. Version 0.5.0']);
    
    try {
        $diff = genDiff($args['<firstFile>'], $args['<secondFile>'], $args['--format']);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        return;
    }
    
    print_r($diff);
}
