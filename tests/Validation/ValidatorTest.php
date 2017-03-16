<?php

namespace Mleczek\CBuilder\Tests\Validation;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Validation\Exceptions\JsonDecodeException;
use Mleczek\CBuilder\Validation\Exceptions\ValidationException;
use Mleczek\CBuilder\Validation\Validator;
use JsonSchema\Validator as ThirdPartyValidator;

class ValidatorTest extends TestCase
{
    public function testValidate()
    {
        $validator = new Validator(new ThirdPartyValidator());

        $json = (object)[
            'a' => 1,
            'b' => [],
            'c' => null,
        ];

        $schema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'a' => (object)[
                    'type' => 'number',
                ],
                'b' => (object)[
                    'type' => 'array',
                ],
            ],
        ];

        // Schema match
        $validator->validate($json, $schema);

        // Schema mismatch
        $json->b = 2;
        $this->expectException(ValidationException::class);
        $validator->validate($json, $schema);
    }

    public function testValidateWithInvalidJson()
    {
        $validator = new Validator(new ThirdPartyValidator());

        $this->expectException(JsonDecodeException::class);
        $validator->validate('{"a" 3}', '{"type": "object"}');
        //                       ^^-- colon missed
    }

    public function testValidateWithInvalidSchema()
    {
        $validator = new Validator(new ThirdPartyValidator());

        $this->expectException(JsonDecodeException::class);
        $validator->validate('{"a": 3}', '{"type" "object"}');
        //                       colon missed --^^
    }
}
