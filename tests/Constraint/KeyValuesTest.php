<?php

namespace Mleczek\CBuilder\Tests\Constraint;

use Mleczek\CBuilder\Constraint\KeyValues;
use Mleczek\CBuilder\Tests\TestCase;

class KeyValuesTest extends TestCase
{
    public function testGetKey()
    {
        $key = 'lorem-lipsum';
        $kv = new KeyValues($key, []);

        $this->assertEquals($key, $kv->getKey());
    }

    public function testHasValue()
    {
        $kv = new KeyValues('', ['v1', 'v2', 'v3']);

        $this->assertTrue($kv->hasValue('v1'));
        $this->assertTrue($kv->hasValue('v2'));
        $this->assertTrue($kv->hasValue('v3'));
        $this->assertFalse($kv->hasValue('v4'));
    }

    public function testHasAnyValue()
    {
        $kv = new KeyValues('', ['v1', 'v2', 'v3']);

        $this->assertTrue($kv->hasAnyValue(['v1', 'v2']));
        $this->assertTrue($kv->hasAnyValue('vn', 'v2'));
        $this->assertTrue($kv->hasAnyValue('v3'));
        $this->assertFalse($kv->hasAnyValue('vm', 'vn'));
    }

    public function testGetValues()
    {
        $values = ['v1', 'v2', 'v3'];
        $kv = new KeyValues('', $values);

        $this->assertEquals($values, $kv->getValues());
    }

    public function testGetValue()
    {
        $values = ['v1', 'v2', 'v3'];
        $kv = new KeyValues('', $values);

        $this->assertEquals($values[0], $kv->getValue(0));
        $this->assertEquals($values[1], $kv->getValue(1));
        $this->assertEquals($values[2], $kv->getValue(2));
    }

    public function testGetNonExistingValue()
    {
        $values = ['v1', 'v2', 'v3'];
        $kv = new KeyValues('', $values);

        $this->assertEquals('default', $kv->getValue(-1, 'default'));
    }

    public function testGetValuesCount()
    {
        $values = ['v1', 'v2', 'v3'];
        $kv = new KeyValues('', $values);

        $this->assertEquals(count($values), $kv->getValuesCount());
    }
}
