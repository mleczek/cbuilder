<?php

namespace Mleczek\CBuilder\Tests\Downloader;

use DI\Container;
use Mleczek\CBuilder\Downloader\Factory;
use Mleczek\CBuilder\Downloader\Providers\LocalDownloader;
use Mleczek\CBuilder\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testMakeLocal()
    {
        $dir = 'temp/lorem';
        $downloader = $this->createMock(LocalDownloader::class);

        $di = $this->createMock(Container::class);
        $di->expects($this->once())
            ->method('make')
            ->with(LocalDownloader::class, ['src' => $dir])
            ->willReturn($downloader);

        $factory = new Factory($di);
        $this->assertEquals($downloader, $factory->makeLocal($dir));
    }
}
