<?php

namespace Mleczek\CBuilder\Tests\Environment;

use Mleczek\CBuilder\Environment\FileExtensions;
use Mleczek\CBuilder\Tests\TestCase;

class FileExtensionsTest extends TestCase
{
    public function testExecutable()
    {
        $ext = $this->createPartialMock(FileExtensions::class, ['isWindows']);

        $ext->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.exe', $ext->executable());
        $this->assertEquals('', $ext->executable());
    }

    public function testStaticLibrary()
    {
        $ext = $this->createPartialMock(FileExtensions::class, ['isWindows']);

        $ext->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.lib', $ext->staticLibrary());
        $this->assertEquals('.a', $ext->staticLibrary());
    }

    public function testSharedLibrary()
    {
        $ext = $this->createPartialMock(FileExtensions::class, ['isWindows']);

        $ext->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.dll', $ext->sharedLibrary());
        $this->assertEquals('.so', $ext->sharedLibrary());
    }
}
