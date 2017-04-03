<?php

namespace Mleczek\CBuilder\Tests\Dependency;

use Mleczek\CBuilder\Dependency\Resolver;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Factory as RepositoriesFactory;
use Mleczek\CBuilder\Dependency\Entities\Factory as EntitiesFactory;
use Mleczek\CBuilder\Tests\TestCase;

class ResolverTest extends TestCase
{
    /**
     * @var RepositoriesFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoriesFactory;

    /**
     * @var EntitiesFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entitiesFactory;

    /**
     * @var Resolver
     */
    protected $resolver;

    public function setUp()
    {
        $this->repositoriesFactory = $this->createMock(RepositoriesFactory::class);
        $this->entitiesFactory = $this->createMock(EntitiesFactory::class);

        $this->resolver = new Resolver($this->repositoriesFactory, $this->entitiesFactory);
    }

    public function testBasicUsage()
    {
        $dependencies = [(object)[
            'name' => 'org/example',
            'version' => '*',
            'linking' => 'static',
        ], (object)[
            'name' => 'org/console',
            'version' => '^1.0',
            'linking' => 'static',
        ]];

        $package = $this->createMock(Package::class);
        $package->expects($this->any())->method('getDependencies')->willreturn($dependencies);
        $package->expects($this->any())->method('getDevDependencies')->willReturn([]);
        $package->method('getRepositories')->willReturn([]); // any array to avoid errors

        $collection = $this->createMock(Collection::class);
        $this->repositoriesFactory->method('hydrate')->willReturn($collection);

        // first level repositories collection results
        $collection->method('find')->willReturnCallback(function ($packageName) {
            $package = $this->createMock(Package::class);
            $package->method('getName')->willReturn($packageName);
            $package->expects($this->never())->method('getDevDependencies'); // nested dev dependencies shouldn't be included
            $package->method('getDependencies')->willReturnCallback(function () use ($packageName) {
                // org/example contains nested dependency org/console
                return $packageName != 'org/example' ? [] : [(object)[
                    'name' => 'org/console',
                    'version' => '1.2.9',
                    'linking' => 'static',
                ]];
            });

            $remote = $this->createMock(Remote::class);
            $remote->method('getPackage')->willReturn($package);

            return $remote;
        });

        $this->entitiesFactory->expects($this->exactly(2))->method('makeTreeNode');
        $this->resolver->resolve($package);

        $result = $this->resolver->getList();
        $this->assertEquals(['*'], $result['org/example']->constraints);
        $this->assertEquals(['1.2.9', '^1.0'], $result['org/console']->constraints);
    }
}
