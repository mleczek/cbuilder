<?php

namespace Mleczek\CBuilder\Tests\Version\Providers;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;
use Mleczek\CBuilder\Version\Providers\ConstVersionResolver;

class ConsVersionResolverTest extends TestCase
{
    /**
     * @var ConstVersionResolver
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new ConstVersionResolver(
            new Comparator()
        );
    }

    public function testHas()
    {
        $this->assertTrue($this->resolver->has('org/package', '1.0.0'));
        $this->assertTrue($this->resolver->has('org/package', '1.0'));
        $this->assertFalse($this->resolver->has('org/package', '0.1'));
        $this->assertFalse($this->resolver->has('org/package', '1.0.1'));
    }

    public function testGet()
    {
        $this->assertEquals(['1.0.0'], $this->resolver->get('org/package'));
    }

    public function testGetSatisfiedBy()
    {
        $this->assertEquals([], $this->resolver->getSatisfiedBy('org/package', '<= 0.1'));
        $this->assertEquals(['1.0.0'], $this->resolver->getSatisfiedBy('org/package', '> 0.1'));
        $this->assertEquals(['1.0.0'], $this->resolver->getSatisfiedBy('org/package', '*'));
    }

    public function testGetGreaterThan()
    {
        $this->assertEquals([], $this->resolver->getGreaterThan('org/package', '1.1'));
        $this->assertEquals(['1.0.0'], $this->resolver->getGreaterThan('org/package', '0.1'));
    }
}
