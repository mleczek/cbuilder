<?php

namespace Mleczek\CBuilder\Tests\Version\Providers;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;
use Mleczek\CBuilder\Version\Providers\ConstVersionFinder;

class ConsVersionFinderTest extends TestCase
{
    /**
     * @var ConstVersionFinder
     */
    private $finder;

    public function setUp()
    {
        $this->finder = new ConstVersionFinder(
            new Comparator(),
            'org/package'
        );
    }

    public function testHas()
    {
        $this->assertTrue($this->finder->has('1.0.0'));
        $this->assertTrue($this->finder->has('1.0'));
        $this->assertFalse($this->finder->has('0.1'));
        $this->assertFalse($this->finder->has('1.0.1'));
    }

    public function testGet()
    {
        $this->assertEquals(['1.0.0'], $this->finder->get());
    }

    public function testGetSatisfiedBy()
    {
        $this->assertEquals([], $this->finder->getSatisfiedBy('<= 0.1'));
        $this->assertEquals(['1.0.0'], $this->finder->getSatisfiedBy('> 0.1'));
        $this->assertEquals(['1.0.0'], $this->finder->getSatisfiedBy('*'));
    }

    public function testGetGreaterThan()
    {
        $this->assertEquals([], $this->finder->getGreaterThan('1.1'));
        $this->assertEquals(['1.0.0'], $this->finder->getGreaterThan('0.1'));
    }
}
