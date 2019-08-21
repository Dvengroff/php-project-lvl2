<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($pathToFile1, $pathToFile2, $prettyDiff, $plainDiff, $jsonDiff)
    {
        $actual = genDiff($pathToFile1, $pathToFile2);
        $expected = file_get_contents($prettyDiff);
        $this->assertEquals($expected, $actual);

        $actual = genDiff($pathToFile1, $pathToFile2, 'plain');
        $expected = file_get_contents($plainDiff);
        $this->assertEquals($expected, $actual);

        $actual = genDiff($pathToFile1, $pathToFile2, 'json');
        $expected = file_get_contents($jsonDiff);
        $this->assertEquals($expected, $actual);
    }

    public function additionProvider()
    {
        return [
            "flat_json" => [
                __DIR__ . "/fixtures/json/before1.json",
                __DIR__ . "/fixtures/json/after1.json",
                __DIR__ . "/fixtures/pretty/diff1",
                __DIR__ . "/fixtures/plain/diff1",
                __DIR__ . "/fixtures/json/diff1.json"
            ],
            "flat_yaml" => [
                __DIR__ . "/fixtures/yaml/before1.yml",
                __DIR__ . "/fixtures/yaml/after1.yml",
                __DIR__ . "/fixtures/pretty/diff1",
                __DIR__ . "/fixtures/plain/diff1",
                __DIR__ . "/fixtures/json/diff1.json"
            ],
            "nested_json" => [
                __DIR__ . "/fixtures/json/before2.json",
                __DIR__ . "/fixtures/json/after2.json",
                __DIR__ . "/fixtures/pretty/diff2",
                __DIR__ . "/fixtures/plain/diff2",
                __DIR__ . "/fixtures/json/diff2.json"
            ],
            "nested_yaml" => [
                __DIR__ . "/fixtures/yaml/before2.yml",
                __DIR__ . "/fixtures/yaml/after2.yml",
                __DIR__ . "/fixtures/pretty/diff2",
                __DIR__ . "/fixtures/plain/diff2",
                __DIR__ . "/fixtures/json/diff2.json"
            ],
            "json_with_array" => [
                __DIR__ . "/fixtures/json/before3.json",
                __DIR__ . "/fixtures/json/after3.json",
                __DIR__ . "/fixtures/pretty/diff3",
                __DIR__ . "/fixtures/plain/diff3",
                __DIR__ . "/fixtures/json/diff3.json"
            ],
            "yaml_with_array" => [
                __DIR__ . "/fixtures/yaml/before3.yml",
                __DIR__ . "/fixtures/yaml/after3.yml",
                __DIR__ . "/fixtures/pretty/diff3",
                __DIR__ . "/fixtures/plain/diff3",
                __DIR__ . "/fixtures/json/diff3.json"
            ]
        ];
    }
}
