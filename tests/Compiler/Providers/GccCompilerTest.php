<?php

namespace Mleczek\CBuilder\Tests\Compiler\Providers;

use Mleczek\CBuilder\Compiler\Providers\GccCompiler;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class GccCompilerTest extends TestCase
{
    public function testBuildExecutable()
    {
        $fs = new Filesystem();
        $gcc = new GccCompiler($fs);

        $fs->touchDir('temp');
        $gcc->setArchitecture('x86')
            ->addMacro('MESSAGE', '"Hello, World!"')
            ->setSourceFiles('resources/fixtures/project/main.cpp')
            ->buildExecutable('temp/output');

        $output = [];
        $exitCode = 1;
        $command = str_replace('/', DIRECTORY_SEPARATOR, 'temp/output');
        exec($command, $output, $exitCode);

        $this->assertEquals(0, $exitCode, 'Compiled program returned with non zero status code.');
        $this->assertEquals('Hello, World!', $output[0]);
    }
}
