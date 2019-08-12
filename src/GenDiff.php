<?php

namespace GenDiff\GenDiff;

const DOC = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOC;

function run()
{
    return \Docopt::handle(DOC);
}
