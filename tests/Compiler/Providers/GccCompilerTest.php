<?php

namespace Mleczek\CBuilder\Tests\Compiler\Providers;

use Mleczek\CBuilder\Compiler\Providers\GccCompiler;
use Mleczek\CBuilder\Environment\FileExtensions;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class GccCompilerTest extends TestCase
{
    /**
     * @var FileExtensions
     */
    protected $ext;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var GccCompiler
     */
    protected $gcc;

    public function setUp()
    {
        $this->ext = new FileExtensions();
        $this->fs = new Filesystem();
        $this->gcc = new GccCompiler($this->fs, $this->ext);
    }

    public function testBuildExecutable86()
    {
        $this->fs->touchDir('temp');
        $this->gcc->setArchitecture('x86')
            ->addMacro('MESSAGE', '"Hello, World!"')
            ->addSourceFiles('resources/fixtures/project/main.cpp')
            ->buildExecutable('temp/output');

        $output = [];
        $exitCode = 1;
        $command = str_replace('/', DIRECTORY_SEPARATOR, 'temp/output');
        exec($command, $output, $exitCode);

        $this->assertEquals(0, $exitCode, 'Compiled program returned with non zero status code.');
        $this->assertEquals('Hello, World!', $output[0]);
    }

    public function testBuildExecutableWithStaticLib86()
    {
        $this->fs->touchDir('temp');
        $this->gcc->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/static-library/src/console.cpp')
            ->addIncludeDirs('resources/fixtures/static-library/include')
            ->buildStaticLibrary('temp/console');

        $this->gcc->reset()
            ->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/linking/static.cpp')
            ->addStaticLibrary('temp/console', 'resources/fixtures/static-library/include')
            ->buildExecutable('temp/output');

        $output = [];
        $exitCode = 1;
        $command = str_replace('/', DIRECTORY_SEPARATOR, 'temp/output');
        exec($command, $output, $exitCode);

        $this->assertEquals(0, $exitCode, 'Compiled program returned with non zero status code.');
        $this->assertEquals('Static linking works!', $output[0]);
    }

    public function testBuildExecutableWithSharedLib86()
    {
        $this->fs->touchDir('temp');
        $this->gcc->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/shared-library/src/codes.cpp')
            ->addIncludeDirs('resources/fixtures/shared-library/include')
            ->buildSharedLibrary('temp/codes');

        $this->gcc->reset()
            ->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/linking/dynamic.cpp')
            ->addSharedLibrary('temp/codes', 'resources/fixtures/shared-library/include')
            ->buildExecutable('temp/output');

        $output = [];
        $exitCode = 1;
        $command = str_replace('/', DIRECTORY_SEPARATOR, 'temp/output');
        exec($command, $output, $exitCode);

        $this->assertEquals(0, $exitCode, 'Compiled program returned with non zero status code.');
        $this->assertEquals('Dynamic linking works!', $output[0]);
    }

    public function testBuildExecutableWithBothLib86()
    {
        $this->fs->touchDir('temp');
        $this->gcc->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/static-library/src/console.cpp')
            ->addIncludeDirs('resources/fixtures/static-library/include')
            ->buildStaticLibrary('temp/console');

        $this->gcc->reset()
            ->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/shared-library/src/codes.cpp')
            ->addIncludeDirs('resources/fixtures/shared-library/include')
            ->buildSharedLibrary('temp/codes');

        $this->gcc->reset()
            ->setArchitecture('x86')
            ->addSourceFiles('resources/fixtures/linking/both.cpp')
            ->addStaticLibrary('temp/console', 'resources/fixtures/static-library/include')
            ->addSharedLibrary('temp/codes', 'resources/fixtures/shared-library/include')
            ->buildExecutable('temp/output');

        $output = [];
        $exitCode = 1;
        $command = str_replace('/', DIRECTORY_SEPARATOR, 'temp/output');
        exec($command, $output, $exitCode);

        $this->assertEquals(0, $exitCode, 'Compiled program returned with non zero status code.');
        $this->assertEquals('Static and dynamic linking works!', $output[0]);
    }
}
