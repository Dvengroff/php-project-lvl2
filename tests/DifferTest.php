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
            "flat_json" => [
                __DIR__ . "/fixtures/json/before1.json",
                __DIR__ . "/fixtures/json/after1.json",
                __DIR__ . "/fixtures/difference1"
            ],
            "flat_yaml" => [
                __DIR__ . "/fixtures/yaml/before1.yml",
                __DIR__ . "/fixtures/yaml/after1.yml",
                __DIR__ . "/fixtures/difference1"
            ],
            "nested_json" => [
                __DIR__ . "/fixtures/json/before2.json",
                __DIR__ . "/fixtures/json/after2.json",
                __DIR__ . "/fixtures/difference2"
            ],
            "nested_yaml" => [
                __DIR__ . "/fixtures/yaml/before2.yml",
                __DIR__ . "/fixtures/yaml/after2.yml",
                __DIR__ . "/fixtures/difference2"
            ],
            "json_with_array" => [
                __DIR__ . "/fixtures/json/before3.json",
                __DIR__ . "/fixtures/json/after3.json",
                __DIR__ . "/fixtures/difference3"
            ],
            "yaml_with_array" => [
                __DIR__ . "/fixtures/yaml/before3.yml",
                __DIR__ . "/fixtures/yaml/after3.yml",
                __DIR__ . "/fixtures/difference3"
            ]
        ];
    }
}
