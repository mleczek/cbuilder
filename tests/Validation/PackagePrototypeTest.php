<?php

namespace Mleczek\CBuilder\Tests\Validation;

use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Validation\Validator;
use JsonSchema\Validator as ThirdPartyValidator;

/**
 * Validate resources/package.prototype.json
 * against the resources/package.schema.json.
 */
class PackagePrototypeTest extends TestCase
{
    public function testPackagePrototype()
    {
        $validator = new Validator(new ThirdPartyValidator());
        $validator->validate(
            file_get_contents(CBUILDER_DIR . '/resources/package.prototype.json'),
            file_get_contents(CBUILDER_DIR . '/resources/package.schema.json')
        );
    }
}
