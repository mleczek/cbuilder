<?php

namespace Mleczek\CBuilder\Tests\Dependency;

use Mleczek\CBuilder\Dependency\Collector;
use Mleczek\CBuilder\Dependency\Exceptions\DependencyNotAvailableException;
use Mleczek\CBuilder\Dependency\Observer;
use Mleczek\CBuilder\Dependency\Resolver;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;
use Mleczek\CBuilder\Version\Finder;

class CollectorTest extends TestCase
{
    /**
     * @var Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observer;

    /**
     * @var Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var Comparator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $comparator;

    /**
     * @var Collector
     */
    protected $collector;

    public function setUp()
    {
        $this->observer = $this->createMock(Observer::class);
        $this->resolver = $this->createMock(Resolver::class);
        $this->factory = $this->createMock(Factory::class);
        $this->comparator = $this->createMock(Comparator::class);
        $this->comparator->method('sort')->willReturnArgument(0);

        // Required only to not throw exceptions by calling tests.
        $package = $this->createMock(Package::class);
        $this->factory->method('makeCurrent')->willReturn($package);

        $this->collector = new Collector($this->observer, $this->resolver, $this->factory, $this->comparator);
    }

    /**
     * @param string $name
     * @param string[] $versions
     * @param string[] $constraints
     * @return object Contains remote and constraints keys.
     */
    private function createResolverListItem($name, array $versions, array $constraints = ['*'])
    {
        $package = $this->createMock(Package::class);
        $package->method('getName')->willReturn($name);

        $finder = $this->createMock(Finder::class);
        $finder->method('getSatisfiedBy')->willReturn($versions);

        $remote = $this->createMock(Remote::class);
        $remote->method('getPackage')->willReturn($package);
        $remote->method('getVersionFinder')->willReturn($finder);

        return (object)[
            'remote' => $remote,
            'constraints' => $constraints,
        ];
    }

    public function testWithoutInstalledDependencies()
    {
        $this->observer->method('getAmbiguous')->willReturn([]);
        $this->resolver->method('getList')->willReturn([
            $this->createResolverListItem('org/package', ['1.0.0', '2.5.7']),
            $this->createResolverListItem('hello/world', ['3.1']),
            $this->createResolverListItem('company/console', ['1.0.0']),
        ]);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals([], $this->collector->getOutdated());
        $this->assertEquals([
            'org/package' => '2.5.7',
            'hello/world' => '3.1',
            'company/console' => '1.0.0',
        ], $this->collector->getMissing());
    }

    public function testUpdateToNewestVersion()
    {
        $listItem = $this->createResolverListItem('org/package', ['1.0.0', '2.5.7'], ['^2.3']);

        $this->observer->method('hasInstalled')->with('org/package')->willReturn(true);
        $this->observer->method('getInstalled')->willReturn(['org/package' => '1.0.0']);
        $this->observer->method('getAmbiguous')->willReturn([]);
        $this->resolver->method('getList')->willReturn([$listItem]);

        $listItem->remote->getVersionFinder()->method('getSatisfiedBy')
            ->with(['^2.3', '>=1.0.0'])->willReturn(['2.5.7']);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals(['org/package' => '2.5.7'], $this->collector->getOutdated());
        $this->assertEquals([], $this->collector->getMissing());
    }

    public function testOnlyLowerVersionsAvailable()
    {
        $this->resolver->method('getList')->willReturn([
            $this->createResolverListItem('org/package', []),
        ]);

        $this->expectException(DependencyNotAvailableException::class);
        $this->collector->collect();
    }

    public function testRedundantPackages()
    {
        $this->observer->method('getAmbiguous')->willReturn(['org/package']);
        $this->resolver->method('getList')->willReturn([]);

        $this->collector->collect();
        $this->assertEquals(['org/package'], $this->collector->getRedundant());
        $this->assertEquals([], $this->collector->getOutdated());
        $this->assertEquals([], $this->collector->getMissing());
    }

    public function testUpdateFromUnknownVersion()
    {
        $this->observer->method('getAmbiguous')->willReturn(['org/package']);
        $this->resolver->method('getList')->willReturn([
            $this->createResolverListItem('org/package', ['2.0']),
        ]);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals(['org/package' => '2.0'], $this->collector->getOutdated());
        $this->assertEquals([], $this->collector->getMissing());
    }
}
