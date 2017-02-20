<?php

namespace Mleczek\CBuilder\Tests\Repository;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testHas()
    {
        $repository = $this->createMock(Repository::class);
        $repository->method('has')->with('org/console')->willReturnOnConsecutiveCalls(true, false);

        $collection = new Collection();
        $collection->add($repository);

        $this->assertTrue($collection->has('org/console'));
        $this->assertFalse($collection->has('org/console'));
    }

    public function testFindExisting()
    {
        $package = $this->createMock(Package::class);

        $repository = $this->createMock(Repository::class);
        $repository->method('has')->with('org/console')->willReturn(true);
        $repository->method('get')->with('org/console')->willReturn($package);

        $collection = new Collection();
        $collection->add($repository);

        $this->assertEquals($package, $collection->find('org/console'));
    }

    public function testFindNotExisting()
    {
        $repository = $this->createMock(Repository::class);
        $repository->method('has')->with('org/console')->willReturn(false);

        $collection = new Collection();
        $collection->add($repository);

        $this->expectException(PackageNotFoundException::class);
        $collection->find('org/console');
    }

    public function testOrderOfSearching()
    {
        $repositoryA = $this->createMock(Repository::class);
        $repositoryB = $this->createMock(Repository::class);

        $repositoryA->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(['org/console'], ['org/json'])
            ->willReturnOnConsecutiveCalls(true, false);

        $repositoryB->expects($this->once())
            ->method('has')
            ->with('org/json')
            ->willReturn(false);

        $collection = new Collection();
        $collection->add($repositoryA);
        $collection->add($repositoryB);

        $this->assertTrue($collection->has('org/console'));
        $this->assertFalse($collection->has('org/json'));
    }

    public function testOrderOfSearching2()
    {
        $package = $this->createMock(Package::class);

        $repositoryA = $this->createMock(Repository::class);
        $repositoryB = $this->createMock(Repository::class);
        $repositoryC = $this->createMock(Repository::class);

        $repositoryA->expects($this->once())->method('has')->with('org/package')->willReturn(false);
        $repositoryB->method('has')->with('org/package')->willReturn(true);
        $repositoryB->method('get')->with('org/package')->willReturn($package);
        $repositoryC->expects($this->never())->method('has');

        $collection = new Collection();
        $collection->add($repositoryA);
        $collection->add($repositoryB);
        $collection->add($repositoryC);

        $this->assertEquals($package, $collection->find('org/package'));
    }
}
