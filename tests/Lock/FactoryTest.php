<?php

namespace Mleczek\CBuilder\Tests\Lock;

use DI\Container;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Factory;
use Mleczek\CBuilder\Lock\Lock;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Validation\Validator;

class FactoryTest extends TestCase
{
    /**
     * @var Container|\PHPUnit_Framework_MockObject_MockObject
     */
    private $di;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fs;

    /**
     * @var Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;

    public function setUp()
    {
        $this->di = $this->createMock(Container::class);
        $this->fs = $this->createMock(Filesystem::class);
        $this->validator = $this->createMock(Validator::class);
    }

    public function testMakeFromFile()
    {
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$this->di, $this->fs, $this->validator])
            ->setMethods(['makeFromJson'])
            ->getMock();

        $file = 'temp/file';
        $this->fs->expects($this->once())
            ->method('existsFile')
            ->with($file)
            ->willReturn(true);

        $fileContent = '{"dependencies": {...}}';
        $this->fs->expects($this->once())
            ->method('readFile')
            ->with($file)
            ->willReturn($fileContent);

        $lock = $this->createMock(Lock::class);
        $factory->expects($this->once())
            ->method('makeFromJson')
            ->with($fileContent)
            ->willReturn($lock);

        $this->assertEquals($lock, $factory->makeFromFileOrEmpty($file));
    }

    public function testMakeEmptyFromFile()
    {
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$this->di, $this->fs, $this->validator])
            ->setMethods(['makeEmpty'])
            ->getMock();

        $file = 'temp/file';
        $this->fs->expects($this->once())
            ->method('existsFile')
            ->with($file)
            ->willReturn(false);

        $lock = $this->createMock(Lock::class);
        $factory->expects($this->once())
            ->method('makeEmpty')
            ->willReturn($lock);

        $this->assertEquals($lock, $factory->makeFromFileOrEmpty($file));
    }

    public function testMakeEmpty()
    {
        $lock = $this->createMock(Lock::class);
        $this->di->expects($this->once())
            ->method('make')
            ->with(Lock::class)
            ->willReturn($lock);

        $factory = new Factory($this->di, $this->fs, $this->validator);
        $this->assertEquals($lock, $factory->makeEmpty());
    }

    public function testMakeFromJson()
    {
        $this->validator->expects($this->once())
            ->method('validate');

        $json = '{"dependencies": {"org/a": "2.1"}}';
        $lock = $this->createMock(Lock::class);
        $this->di->expects($this->once())
            ->method('make')
            ->with(Lock::class, ['json' => json_decode($json)])
            ->willReturn($lock);

        $factory = new Factory($this->di, $this->fs, $this->validator);
        $this->assertEquals($lock, $factory->makeFromJson($json));
    }
}
