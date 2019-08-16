<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($pathToFile1, $pathToFile2, $diffString)
    {
        $expected = file_get_contents($diffString);
        $actual = genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected, $actual);
    }

    public function additionProvider()
    {
        return [
            "json" => [
                __DIR__ . "/fixtures/json/before.json",
                __DIR__ . "/fixtures/json/after.json",
                __DIR__ . "/fixtures/difference"
            ],
            "yaml" => [
                __DIR__ . "/fixtures/yaml/before.yml",
                __DIR__ . "/fixtures/yaml/after.yml",
                __DIR__ . "/fixtures/difference"
            ]
        ];
    }
}
