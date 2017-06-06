<?php

namespace Mleczek\CBuilder\Tests\Repository;

use DI\Container;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Exceptions\HydratePropertyNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\UnknownRepositoryTypeException;
use Mleczek\CBuilder\Repository\Factory;
use Mleczek\CBuilder\Repository\Providers\EmptyRepository;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;
use Mleczek\CBuilder\Package\Factory as PackageFactory;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testMakeLocal()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $repository = $this->createMock(LocalRepository::class);
        $packageFactory = $this->createMock(PackageFactory::class);

        $di->expects($this->once())
            ->method('make')
            ->with(LocalRepository::class, ['src' => 'temp/dir'])
            ->willReturn($repository);

        $factory = new Factory($di, $config, $packageFactory);
        $this->assertEquals($repository, $factory->makeLocal('temp/dir'));
    }

    public function testMakeEmpty()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $packageFactory = $this->createMock(PackageFactory::class);

        $repository = $this->createMock(LocalRepository::class);
        $di->expects($this->once())
            ->method('make')
            ->with(EmptyRepository::class)
            ->willReturn($repository);

        $factory = new Factory($di, $config, $packageFactory);
        $this->assertEquals($repository, $factory->makeEmpty('temp/dir'));
    }

    public function testHydrateCurrent()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $packageFactory = $this->createMock(PackageFactory::class);

        $repositories = ['any array value to check correctness'];
        $package = $this->createMock(Package::class);
        $package->method('getRepositories')->willReturn($repositories);
        $packageFactory->method('makeCurrent')->willReturn($package);

        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$di, $config, $packageFactory])
            ->setMethods(['hydrate'])->getMock();

        $collection = $this->createMock(Collection::class);
        $factory->expects($this->once())->method('hydrate')
            ->with($repositories)->willReturn($collection);

        $this->assertEquals($collection, $factory->hydrateCurrent());
    }

    public function testHydrate()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $packageFactory = $this->createMock(PackageFactory::class);

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
            ->withConsecutive(
                [Collection::class],
                [$namespace, ['src' => 'local/dir']],
                [$namespace, ['src' => 'other/local/dir']]
            )->willReturnOnConsecutiveCalls(
                $collection, $repoA, $repoB
            );

        // At the end repositories will be added to the collection
        // (keeping the order in which they was passed to the factory).
        $collection->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive([$repoA], [$repoB]);

        $factory = new Factory($di, $config, $packageFactory);
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
        $packageFactory = $this->createMock(PackageFactory::class);

        $config->method('has')
            ->with('repositories.local')
            ->willReturn(false);

        $this->expectException(UnknownRepositoryTypeException::class);

        $factory = new Factory($di, $config, $packageFactory);
        $factory->hydrate([[
            'type' => 'local',
            'src' => 'https://localhost',
        ]]);
    }

    public function testHydrateInvalidTypeKey()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $packageFactory = $this->createMock(PackageFactory::class);

        $this->expectException(HydratePropertyNotFoundException::class);

        $factory = new Factory($di, $config, $packageFactory);
        $factory->hydrate([[
            'src' => 'https://localhost',
        ]]);
    }

    public function testHydrateInvalidSrcKey()
    {
        $di = $this->createMock(Container::class);
        $config = $this->createMock(Config::class);
        $packageFactory = $this->createMock(PackageFactory::class);

        $this->expectException(HydratePropertyNotFoundException::class);

        $factory = new Factory($di, $config, $packageFactory);
        $factory->hydrate([[
            'type' => 'local',
        ]]);
    }
}
