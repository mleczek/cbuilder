<?php

namespace Mleczek\CBuilder\Tests\Version\Comparator;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Comparator;

class SortMethodTest extends TestCase
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
     * @param string[] $sorted
     * @dataProvider dataProvider
     */
    public function testData($versions, $sorted)
    {
        $this->assertEquals($sorted, $this->comparator->sort($versions));
    }

    public function dataProvider()
    {
        return [
            // [<versions>, <sorted>]
            [['1.0.0', '2.1', '3.2'], ['1.0.0', '2.1', '3.2']],
            [['1.9.9', '2.0.0', '1.0', '1.5.12', '0.9', '1.0.2'], ['0.9', '1.0', '1.0.2', '1.5.12', '1.9.9', '2.0.0']],
            [['12.5.0', '2.5.3', '1.3.7', '2.0', '13', '13.4.12'], ['1.3.7', '2.0', '2.5.3', '12.5.0', '13', '13.4.12']],
            [['0.0.1', '5.2.0', '0.0.2', '1.2', '50.0.50'], ['0.0.1', '0.0.2', '1.2', '5.2.0', '50.0.50']],
            [['1.0.0', '0.1', '0.0.1'], ['0.0.1', '0.1', '1.0.0']],
        ];
    }
}
