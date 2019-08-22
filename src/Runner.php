<?php

namespace GenDiff\Runner;

use function GenDiff\Differ\genDiff;

function run($doc)
{
    $args = \Docopt::handle($doc, ['version' => 'GenDiff. Version 0.4.0']);
    $format = isset($args['--format']) ? $args['--format'] : null;
    try {
        $diff = genDiff($args['<firstFile>'], $args['<secondFile>'], $format);
    } catch (\Exception $e) {
        exit($e->getMessage() . PHP_EOL);
    }
    
    print_r($diff);
}
