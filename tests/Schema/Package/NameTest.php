<?php


namespace Mleczek\CBuilder\Tests\Schema\Package;


use JsonSchema\Validator;
use Mleczek\CBuilder\Tests\TestCase;

class NameTest extends TestCase
{
    const KEY_NAME = 'name';

    /**
     * Path to the json containing schema
     * to validate package json file.
     */
    const SCHEMA_PATH = self::PROJECT_DIR . '/resources/package.schema.json';

    /**
     * Each row contains:
     * <dataSetName> => [<value>, <isValid>],
     *
     * @return array
     */
    public function valuesProvider()
    {
        return [
            'array' => [[], false],
            'number' => [5.28, false],
            'null' => [null, false],
            'bool' => [false, false],
            'object' => [(object)[], false],
            'one part #1' => ['org', false],
            'one part #2' => ['package', false],
            'common' => ['org/package', true],
            'short #1' => ['o/package', false],
            'short #2' => ['or/package', false],
            'short #3' => ['org/package', true],
            'dots' => ['o.o/package', false],
            'slashes' => ['org/org/package', false],
            'upper #1' => ['lower/Upper', false],
            'upper #2' => ['Upper/lower', false],
            'upper #3' => ['uPPer/lower', false],
            'underscore #1' => ['under_score/under_score', true],
            'underscore #2' => ['_underscore/_underscore', false],
            'underscore #3' => ['underscore_/underscore_', false],
            'underscore #4' => ['_/underscore_', false],
            'numbers #1' => ['num0bers/num0bers', true],
            'numbers #2' => ['0numbers/0numbers', true],
            'numbers #3' => ['numbers0/numbers0', true],
            'numbers #4' => ['123/package', true],
            'dash #1' => ['da-sh/da-sh', true],
            'dash #2' => ['-dash/-dash', false],
            'dash #3' => ['dash-/dash-', false],
            'dash #4' => ['-/dash-', false],
        ];
    }

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Called before each test is executed.
     */
    protected function setUp()
    {
        $this->validator = new Validator();
    }

    /**
     * @dataProvider valuesProvider
     * @param mixed $name
     * @param bool $isValid
     */
    public function testValues($name, $isValid)
    {
        $json = (object)[self::KEY_NAME => $name];
        $schema = json_decode(file_get_contents(self::SCHEMA_PATH));

        $this->validator->check($json, $schema);
        $this->assertEquals($isValid, $this->validator->isValid());
    }
}