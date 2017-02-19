<?php

namespace Mleczek\CBuilder\Tests\Constraint;

use DI\Container;
use DI\ContainerBuilder;
use Mleczek\CBuilder\Constraint\Parser;
use Mleczek\CBuilder\Tests\TestCase;

class ParserTest extends TestCase
{
    public function keyValues()
    {
        return [
            'only key' => ['key', 'key', []],
            'single value' => ['key:value', 'key', ['value']],
            'a few values' => ['key:v1,v2,v3,v4', 'key', ['v1', 'v2', 'v3', 'v4']],
            'double colon' => ['key:v1:v2,v3', 'key', ['v1:v2', 'v3']],
            'comma before colon' => ['key,comma:v1,v2', 'key,comma', ['v1', 'v2']],
            'trailing spaces' => ['key : v1 ,v2', 'key', ['v1', 'v2']],
        ];
    }

    /**
     * @dataProvider keyValues
     */
    public function testParse($str, $key, $values)
    {
        $builder = new ContainerBuilder();
        $di = $builder->build();

        $parser = new Parser($di);
        $kv = $parser->parse($str);

        $this->assertEquals($key, $kv->getKey());
        $this->assertEquals($values, $kv->getValues());
    }
}
