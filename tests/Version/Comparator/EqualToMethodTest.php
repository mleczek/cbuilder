<?php

namespace Mleczek\CBuilder\Tests\Version\Comparator;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;

class EqualToMethodTest extends TestCase
{
    /**
     * @var Comparator
     */
    private $comparator;

    public function setUp()
    {
        $this->comparator = new Comparator();
    }

    /**
     * @param string $v1
     * @param string $v2
     * @dataProvider positiveDataProvider
     */
    public function testPositive($v1, $v2)
    {
        $this->assertTrue($this->comparator->equalTo($v1, $v2), "The 'v$v1 = v$v2' should be true");
    }

    /**
     * @param string $v1
     * @param string $v2
     * @dataProvider negativeDataProvider
     */
    public function testNegative($v1, $v2)
    {
        $this->assertFalse($this->comparator->equalTo($v1, $v2), "The 'v$v1 = v$v2' should be false");
    }

    public function positiveDataProvider()
    {
        return [
            ['1.0.0', '1.0.0'],
            ['2.1.19', '2.1.19'],
            ['1.0', '1.0.0'],
            ['1', '1.0'],
        ];
    }

    public function negativeDataProvider()
    {
        return [
            ['2.1.13', '2.1'],
            ['2.1.13', '2.1.14'],
        ];
    }
}
