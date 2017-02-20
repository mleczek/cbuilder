<?php

namespace Mleczek\CBuilder\Tests\Repository;

use DI\Container;
use DI\ContainerBuilder;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Exceptions\HydratePropertyNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\UnknownRepositoryTypeException;
use Mleczek\CBuilder\Repository\Factory;
use Mleczek\CBuilder\Repository\Providers\EmptyRepository;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testMakeLocal()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $repository = $this->createMock(LocalRepository::class);

        $di->expects($this->once())
            ->method('make')
            ->with(LocalRepository::class)
            ->willReturn($repository);

        $repository->expects($this->once())
            ->method('setSource')
            ->with('temp/dir');

        $factory = new Factory($di, $config);
        $this->assertEquals($repository, $factory->makeLocal('temp/dir'));
    }

    public function testMakeEmpty()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);

        $repository = $this->createMock(LocalRepository::class);
        $di->expects($this->once())
            ->method('make')
            ->with(EmptyRepository::class)
            ->willReturn($repository);

        $repository->expects($this->never())
            ->method('setSource');

        $factory = new Factory($di, $config);
        $this->assertEquals($repository, $factory->makeEmpty('temp/dir'));
    }

    public function testHydrate()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);

        // Repository type exists,
        $config->method('has')
            ->with('repositories.local')
            ->willReturn(true);

        // And return repository class namespace.
        $namespace = '\Example\Namespace\Object';
        $config->method('get')
            ->with('repositories.local')
            ->willReturn($namespace);

        // Now DI will make collection and repositories,
        $collection = $this->createMock(Collection::class);
        $repoA = $this->createMock(Repository::class);
        $repoB = $this->createMock(Repository::class);
        $di->expects($this->exactly(3))
            ->method('make')
            ->withConsecutive([Collection::class], [$namespace], [$namespace])
            ->willReturnOnConsecutiveCalls($collection, $repoA, $repoB);

        // And initialize this repositories with matching source.
        $repoA->expects($this->once())
            ->method('setSource')
            ->with('local/dir');

        $repoB->expects($this->once())
            ->method('setSource')
            ->with('other/local/dir');

        // At the end repositories will be added to the collection
        // (keeping the order in which they was passed to the factory).
        $collection->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive([$repoA], [$repoB]);

        $factory = new Factory($di, $config);
        $factory->hydrate([[
            'type' => 'local',
            'src' => 'local/dir',
        ], [
            'type' => 'local',
            'src' => 'other/local/dir',
        ]]);
    }

    public function testHydrateInvalidType()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);

        $config->method('has')
            ->with('repositories.local')
            ->willReturn(false);

        $this->expectException(UnknownRepositoryTypeException::class);

        $factory = new Factory($di, $config);
        $factory->hydrate([[
            'type' => 'local',
            'src' => 'https://localhost',
        ]]);
    }

    public function testHydrateInvalidTypeKey()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);

        $this->expectException(HydratePropertyNotFoundException::class);

        $factory = new Factory($di, $config);
        $factory->hydrate([[
            'src' => 'https://localhost',
        ]]);
    }

    public function testHydrateInvalidSrcKey()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);

        $this->expectException(HydratePropertyNotFoundException::class);

        $factory = new Factory($di, $config);
        $factory->hydrate([[
            'type' => 'local',
        ]]);
    }
}
