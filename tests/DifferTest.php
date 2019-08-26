<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    private $types;
    private $formatters;

    public function setUp(): void
    {
        $this->types = ['json', 'yml'];
        $this->formatters = ['pretty', 'plain', 'json'];
    }
 
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($fileName1, $fileName2, $resFileName)
    {
        foreach ($this->types as $type) {
            $dataDirName = __DIR__ . "/fixtures/{$type}";
            $pathToFile1 = $dataDirName . DIRECTORY_SEPARATOR . "{$fileName1}.{$type}";
            $pathToFile2 = $dataDirName . DIRECTORY_SEPARATOR . "{$fileName2}.{$type}";
            
            foreach ($this->formatters as $format) {
                $actual = genDiff($pathToFile1, $pathToFile2, $format);
                $resDirName = __DIR__ . "/fixtures/results/{$format}";
                $pathToResFile = $resDirName . DIRECTORY_SEPARATOR . "{$resFileName}.{$format}";
                $expected = file_get_contents($pathToResFile);
                $this->assertEquals($expected, $actual);
            }
        }
    }

    public function additionProvider()
    {
        return [
            "flat_data" => ['before1', 'after1', 'diff1'],
            "nested_data" => ['before2', 'after2', 'diff2'],
            "data_with_array" => ['before3', 'after3', 'diff3']
        ];
    }
}
