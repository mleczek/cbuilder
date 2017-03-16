<?php

namespace Mleczek\CBuilder\Tests\Validation;

use Mleczek\CBuilder\Tests\TestCase;
use JsonSchema\Validator as ThirdPartyValidator;
use Mleczek\CBuilder\Validation\Validator;

/**
 * Validate resources/package.prototype.json
 * against the resources/package.schema.json.
 */
class PackageLockPrototypeTest extends TestCase
{
    public function testPackagePrototype()
    {
        $validator = new Validator(new ThirdPartyValidator());
        $validator->validate(
            file_get_contents(self::ROOT_DIR . '/resources/package-lock.prototype.json'),
            file_get_contents(self::ROOT_DIR . '/resources/package-lock.schema.json')
        );
    }
}
