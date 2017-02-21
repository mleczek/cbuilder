<?php

namespace Mleczek\CBuilder\Validation;

use Mleczek\CBuilder\Validation\Exceptions\JsonDecodeException;
use Mleczek\CBuilder\Validation\Exceptions\ValidationException;
use JsonSchema\Validator as ThirdPartyValidator;

class Validator
{
    /**
     * @var ThirdPartyValidator
     */
    private $validator;

    /**
     * Validator constructor.
     *
     * @param ThirdPartyValidator $validator
     */
    public function __construct(ThirdPartyValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Ensure that json match given schema.
     *
     * @param string|object $json
     * @param string|object $schema
     * @throws ValidationException
     * @throws JsonDecodeException
     */
    public function validate($json, $schema)
    {
        $json = $this->jsonDecode($json);
        $schema = $this->jsonDecode($schema);

        $this->validator->check($json, $schema);
        if (!$this->validator->isValid()) {
            $error = $this->validator->getErrors()[0];
            throw new ValidationException("Json not match given schema, the '{$error['property']}' property failed with message: '{$error['message']}'.");
        }
    }

    /**
     * @param string|object $json
     * @return object
     * @throws JsonDecodeException
     */
    private function jsonDecode($json)
    {
        // Skip if already decoded.
        if (is_object($json)) {
            return $json;
        }

        $result = json_decode($json);
        if (!is_object($result)) {
            throw new JsonDecodeException("Json cannot be decoded.");
        }

        return $result;
    }
}
