<?php

namespace Mleczek\CBuilder\Tests\Version\Comparator;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;

class LessThanMethodTest extends TestCase
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
        $this->assertTrue($this->comparator->lessThan($v1, $v2), "The 'v$v1 < v$v2' should be true");
    }

    /**
     * @param string $v1
     * @param string $v2
     * @dataProvider negativeDataProvider
     */
    public function testNegative($v1, $v2)
    {
        $this->assertFalse($this->comparator->lessThan($v1, $v2), "The 'v$v1 < v$v2' should be true");
    }

    public function positiveDataProvider()
    {
        return [
            ['2.3', '2.4'],
            ['2.3', '2.4.15'],
            ['2.3.10', '2.4'],
        ];
    }

    public function negativeDataProvider()
    {
        return [
            ['2.5.15', '2.5.2'],
            ['2.40', '2.39'],
            ['2.5.3', '2.5'],
            ['2.3', '2.3'],
            ['2', '1.9.20'],
        ];
    }
}
