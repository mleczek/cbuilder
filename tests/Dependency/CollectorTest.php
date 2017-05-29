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
     * @var Package|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $package;

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
        $this->package = $this->createMock(Package::class);
        $this->factory->method('makeCurrent')->willReturn($this->package);

        $this->collector = new Collector($this->observer, $this->resolver, $this->factory, $this->comparator);
    }

    /**
     * @param string $name
     * @param string[] $versions
     * @param string $constraints
     * @return object Contains remote and constraints keys.
     */
    private function createResolverListItem($name, array $versions, $constraints = '*')
    {
        $package = $this->createMock(Package::class);
        $package->method('getName')->willReturn($name);

        $finder = $this->createMock(Finder::class);
        if($constraints === '*') {
            $finder->method('getSatisfiedBy')->with($constraints)->willReturn($versions);
        }

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
        $this->package->method('getDependencies')->willReturn([(object)[
            'name' => 'org/package', 'version' => '2.*', 'linking' => ['static', 'dynamic']
        ]]);

        $this->observer->method('hasInstalled')->with('org/package')->willReturn(true);
        $this->observer->method('getInstalled')->willReturn(['org/package' => '1.0.0']);
        $this->observer->method('getAmbiguous')->willReturn([]);
        $this->resolver->method('getList')->willReturn([$listItem]);

        $this->comparator->method('satisfies')
            ->withConsecutive(['1.0.0', '2.*'], ['1.0.0', '^2.3'])
            ->willReturnOnConsecutiveCalls(false, false);

        $listItem->remote->getVersionFinder()->method('getSatisfiedBy')
            ->with('^2.3 >=1.0.0')->willReturn(['2.5.7']);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals(['org/package' => '2.5.7'], $this->collector->getOutdated());
        $this->assertEquals([], $this->collector->getMissing());
    }

    public function testKeepInstalledVersion()
    {
        $listItem = $this->createResolverListItem('org/package', ['0.6.2', '1.0.0', '2.5.7'], ['>0.6']);
        $this->package->method('getDependencies')->willReturn([(object)[
            'name' => 'org/package', 'version' => '>=1.0', 'linking' => ['static', 'dynamic']
        ]]);

        $this->observer->method('hasInstalled')->with('org/package')->willReturn(true);
        $this->observer->method('getInstalled')->willReturn(['org/package' => '1.0.0']);
        $this->observer->method('getAmbiguous')->willReturn([]);
        $this->resolver->method('getList')->willReturn([$listItem]);

        $this->comparator->method('satisfies')
            ->with('1.0.0', '>=1.0')->willreturn(true);

        $listItem->remote->getVersionFinder()->method('getSatisfiedBy')
            ->with('1.0.0')->willReturn(['1.0.0']);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals([], $this->collector->getOutdated());
        $this->assertEquals([], $this->collector->getMissing());
    }

    public function testKeepInstalledVersionAlt()
    {
        $listItem = $this->createResolverListItem('org/package', ['0.6.2', '1.0.0', '2.5.7'], ['>0.6']);
        $this->package->method('getDependencies')->willReturn([]);

        $this->observer->method('hasInstalled')->with('org/package')->willReturn(true);
        $this->observer->method('getInstalled')->willReturn(['org/package' => '1.0.0']);
        $this->observer->method('getAmbiguous')->willReturn([]);
        $this->resolver->method('getList')->willReturn([$listItem]);

        $this->comparator->method('satisfies')
            ->withConsecutive(['1.0.0', '>0.6'])
            ->willReturnOnConsecutiveCalls(true, true);

        $listItem->remote->getVersionFinder()->method('getSatisfiedBy')
            ->with('1.0.0')->willReturn(['1.0.0']);

        $this->collector->collect();
        $this->assertEquals([], $this->collector->getRedundant());
        $this->assertEquals([], $this->collector->getOutdated());
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
