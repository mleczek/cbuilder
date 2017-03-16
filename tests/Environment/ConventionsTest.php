<?php

namespace Mleczek\CBuilder\Tests\Environment;

use Mleczek\CBuilder\Environment\Conventions;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class ConventionsTest extends TestCase
{
    /**
     * @var Conventions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conv;

    public function setUp()
    {
        $this->conv = $this->getMockBuilder(Conventions::class)
            ->setConstructorArgs([new Filesystem()])
            ->setMethods(['isWindows'])
            ->getMock();
    }

    public function testExeExt()
    {
        $this->conv->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.exe', $this->conv->getExeExt());
        $this->assertEquals('', $this->conv->getExeExt());
    }

    public function testStaticLibExt()
    {
        $this->conv->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.lib', $this->conv->getStaticLibExt());
        $this->assertEquals('.a', $this->conv->getStaticLibExt());
    }

    public function testSharedLibExt()
    {
        $this->conv->method('isWindows')->willReturnOnConsecutiveCalls(true, false);
        $this->assertEquals('.dll', $this->conv->toSharedLibExt());
        $this->assertEquals('.so', $this->conv->toSharedLibExt());
    }

    public function testExePathWindows()
    {
        $this->conv->method('isWindows')->willReturn(true);
        $this->assertEquals('temp/name.exe', $this->conv->getExePath('temp/name'));
    }

    public function testExePathLinux()
    {
        $this->conv->method('isWindows')->willReturn(false);
        $this->assertEquals('temp/name', $this->conv->getExePath('temp/name'));
    }

    public function testStaticLibPathWindows()
    {
        $this->conv->method('isWindows')->willReturn(true);
        $this->assertEquals('temp/name.lib', $this->conv->toStaticLibPath('temp/name'));
    }

    public function testStaticLibPathLinux()
    {
        $this->conv->method('isWindows')->willReturn(false);
        $this->assertEquals('temp/libname.a', $this->conv->toStaticLibPath('temp/name'));
    }

    public function testSharedLibPathWindows()
    {
        $this->conv->method('isWindows')->willReturn(true);
        $this->assertEquals('temp/name.dll', $this->conv->toSharedLibPath('temp/name'));
    }

    public function testSharedLibPathLinux()
    {
        $this->conv->method('isWindows')->willReturn(false);
        $this->assertEquals('temp/libname.so', $this->conv->toSharedLibPath('temp/name'));
    }
}
