<?php

namespace Mleczek\CBuilder\Tests\Version;

use DI\Container;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Factory;
use Mleczek\CBuilder\Version\Providers\ConstVersionFinder;

class FactoryTest extends TestCase
{
    public function testMakeConst()
    {
        $finder = $this->createMock(ConstVersionFinder::class);

        $di = $this->createMock(Container::class);
        $di->expects($this->once())
            ->method('make')
            ->with(ConstVersionFinder::class)
            ->willReturn($finder);

        $factory = new Factory($di);
        $this->assertEquals($finder, $factory->makeConst());
    }
}
