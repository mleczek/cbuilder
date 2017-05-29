<?php

namespace Mleczek\CBuilder\Tests\Version\Comparator;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;

class SatisfiesMethodTest extends TestCase
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
     * @param string $v
     * @param string $constraint
     * @dataProvider positiveDataProvider
     */
    public function testPositive($v, $constraint)
    {
        $this->assertTrue($this->comparator->satisfies($v, $constraint), "The 'v$v' should match '$constraint' constraint");
    }

    /**
     * @param string $v
     * @param string $constraint
     * @dataProvider negativeDataProvider
     */
    public function testNegative($v, $constraint)
    {
        $this->assertFalse($this->comparator->satisfies($v, $constraint), "The 'v$v' should NOT match '$constraint' constraint");
    }

    public function positiveDataProvider()
    {
        return [
            ['1.0.0', '1.0.0'],
            ['1.9.9', '^1.0.0'],
            ['1.0.0', '^1.0'],
            ['1.0', '^1.0.0'],
            ['1.9', '^1.0.0'],
            ['1.9', '^1.0'],
            ['1.1.9', '~1.0'],
            ['1.9.9', '~1.0'],
            ['1.0.9', '~1.0.0'],
            ['1.0.9', '~1.0.0'],
            ['1.0.9', '>= 1.0.9'],
            ['1.1.9', '>=1.0.9'],
            ['1.1.9', '>1.0'],
            ['1.1.9', '<1.2'],
            ['1.2.0', '<=1.2'],
            ['1.1', '<1.2 || >1.4'],
            ['1.5', '<1.2 || >1.4'],
            ['1.5', '>=1.2 <=1.5'],
            ['1.5', '*'],
            ['1.5', '1.*'],
        ];
    }

    public function negativeDataProvider()
    {
        return [
            ['0.1', '1.0.0'],
            ['2.0.0', '^1.0.0'],
            ['0.9.0', '^1.0'],
            ['2.9', '^1.0.0'],
            ['12.9', '^1.0.0'],
            ['2.0', '^1.0'],
            ['2.0.0', '~1.0'],
            ['0.9.0', '~1.0'],
            ['1.1.0', '~1.0.0'],
            ['0.0.9', '~1.0.0'],
            ['1.0.8', '>= 1.0.9'],
            ['0.1.9', '>=1.0.9'],
            ['1.0.0', '>1.0'],
            ['1.2.9', '<1.2'],
            ['1.2.1', '<=1.2'],
            ['1.2.2', '<1.2 || >1.4'],
            ['1.3', '<1.2 || >1.4'],
            ['1.5', '>=1.2 <1.5'],
            ['2.3', '1.*'],
        ];
    }
}
