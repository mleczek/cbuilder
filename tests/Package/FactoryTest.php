<?php

namespace Mleczek\CBuilder\Tests\Package;

use DI\Container;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Validation\Validator;

class FactoryTest extends TestCase
{
    /**
     * @var Container|\PHPUnit_Framework_MockObject_MockObject
     */
    private $di;

    /**
     * @var Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fs;

    /**
     * Called before each test is executed.
     */
    protected function setUp()
    {
        $this->di = $this->createMock(Container::class);
        $this->fs = $this->createMock(Filesystem::class);
        $this->config = $this->createMock(Config::class);
        $this->validator = $this->createMock(Validator::class);
    }

    public function testCurrent()
    {
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$this->di, $this->fs, $this->config, $this->validator])
            ->setMethods(['fromDir'])
            ->getMock();

        $factory->expects($this->once())
            ->method('fromDir')
            ->with('.');

        $factory->current();
    }

    public function testFromDir()
    {
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$this->di, new Filesystem(), $this->config, $this->validator])
            ->setMethods(['fromFile'])
            ->getMock();

        $dir = 'temp/dir';
        $filename = 'example.json';
        $this->config->method('get')
            ->with('package.filename')
            ->willReturn($filename);

        $path = "$dir/$filename";
        $this->fs->method('path')
            ->with($dir, $filename)
            ->willReturn($path);

        $factory->expects($this->once())
            ->method('fromFile')
            ->with($path);

        $factory->fromDir($dir);
    }

    public function testFromFile()
    {
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$this->di, $this->fs, $this->config, $this->validator])
            ->setMethods(['fromJson'])
            ->getMock();

        $file = 'temp/dir/example.json';
        $json = '{"ok": true}';
        $this->fs->method('readFile')
            ->with($file)
            ->willReturn($json);

        $factory->expects($this->once())
            ->method('fromJson')
            ->with($json);

        $factory->fromFile($file);
    }

    public function testFromJson()
    {
        $jsonStr = '{"a": 3}';
        $jsonObj = (object)['a' => 3];

        $this->validator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive([$jsonStr], [$jsonObj]);

        $result = $this->createMock(Package::class);
        $this->di->expects($this->exactly(2))
            ->method('make')
            ->withConsecutive(
                [Package::class, ['json' => json_decode($jsonStr)]],
                [Package::class, ['json' => json_decode($jsonStr)]]
            )->willReturn($result);

        $factory = new Factory($this->di, $this->fs, $this->config, $this->validator);
        $this->assertEquals($result, $factory->fromJson($jsonStr));
        $this->assertEquals($result, $factory->fromJson($jsonObj));
    }
}
