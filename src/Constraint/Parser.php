<?php

namespace Mleczek\CBuilder\Constraint;

use DI\Container;
use Mleczek\CBuilder\Constraint\KeyValues;

/**
 * Parse some commands arguments and json keys or values.
 *
 * Examples:
 * - "key:value1,value2"
 * - "after-build:windows,x86"
 * - "^5.3.19:static"
 */
class Parser
{
    const KEY_VALUES_DELIMITER = ':';

    const VALUES_DELIMITER = ',';

    /**
     * @var Container
     */
    private $di;

    /**
     * Parser constructor.
     *
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * Find key and values in given string
     *
     * @param string $str
     * @return \Mleczek\CBuilder\Constraint\KeyValues
     */
    public function parse($str)
    {
        $parts = explode(self::KEY_VALUES_DELIMITER, $str, 2);

        $key = trim($parts[0]);
        $values = [];

        if (isset($parts[1]) && !empty($parts[1])) {
            $values = explode(self::VALUES_DELIMITER, $parts[1]);
            $values = array_map('trim', $values);
        }

        return $this->di->make(KeyValues::class, [
            'key' => $key,
            'values' => $values
        ]);
    }
}
