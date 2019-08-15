<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    protected $pathToFile1, $pathToFile2, $diffString;

    protected function setUp(): void
    {
        $this->pathToFile1 = __DIR__ . "/fixtures/before.json";
        $this->pathToFile2 = __DIR__ . "/fixtures/after.json";
        $this->diffString = file_get_contents(__DIR__ . "/fixtures/difference");
    }

    public function testGenDiff()
    {
        $expected = $this->diffString;
        $actual = genDiff($this->pathToFile1, $this->pathToFile2);
        $this->assertEquals($expected, $actual);
    }
}
