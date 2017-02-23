<?php

namespace Mleczek\CBuilder\Tests\Version\Comparator;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;

class SatisfiedByMethodTest extends TestCase
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
     * @param string[] $versions
     * @param string $constraint
     * @param string[] $expected
     * @dataProvider dataProvider
     */
    public function testData($versions, $constraint, $expected)
    {
        $this->assertEquals($expected, $this->comparator->satisfiedBy($versions, $constraint));
    }

    public function dataProvider()
    {
        return [
            // [<versions>, <constraint>, <expected>]
            [['1.0.0', '1.0', '1', '2.1'], '1.0.0', ['1.0.0', '1.0', '1']],
            [['1.9.9', '2.0.0', '1.0', '1.5.12', '0.9', '1.0.2'], '^1.0.0', ['1.9.9', '1.0', '1.5.12', '1.0.2']],
            [['1.9.9', '2.0.0', '1.0', '1.5.12', '0.9', '1.0.2'], '^1.0', ['1.9.9', '1.0', '1.5.12', '1.0.2']],
            [['1.9.9', '2.0.0', '1.0', '1.5.12', '0.9', '1.0.2'], '~1.0', ['1.9.9', '1.0', '1.5.12', '1.0.2']],
            [['1.9.9', '2.0.0', '1.0', '1.5.12', '0.9', '1.0.2'], '~1.0.0', ['1.0', '1.0.2']],
            [['1.0.9', '0.5', '2.0'], '>= 1.0.9', ['1.0.9', '2.0']],
            [['0.9', '0.0.5'], '>1.0', []],
            [['1.1.9', '2.0', '15.0.1'], '<1.2', ['1.1.9']],
            [['1.2.0', '1.2.1'], '<=1.2', ['1.2.0']],
            [['1.3.5', '1.5'], '<1.2 || >1.4', ['1.5']],
            [['1.5', '0.5', '0.0.1'], '*', ['1.5', '0.5', '0.0.1']],
            [['1.5', '2.5', '1.0.0'], '1.*', ['1.5', '1.0.0']],
        ];
    }
}
