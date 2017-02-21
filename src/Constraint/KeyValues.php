<?php

namespace Mleczek\CBuilder\Constraint;

class KeyValues
{
    /**
     * @var string
     */
    private $key = null;

    /**
     * @var string[]
     */
    private $values = [];

    /**
     * KeyValues constructor.
     *
     * @param string $key
     * @param string[] $values
     */
    public function __construct($key, array $values)
    {
        $this->key = $key;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function hasValue($value)
    {
        return in_array($value, $this->getValues());
    }

    /**
     * @param array|array... $values
     * @return bool True if contains any of the given values, false otherwise.
     */
    public function hasAnyValue($values)
    {
        if(!is_array($values)) {
            $values = func_get_args();
        }

        foreach($values as $value) {
            if($this->hasValue($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Get value at nth position.
     *
     * @param int $nth
     * @param string $default
     * @return string
     */
    public function getValue($nth, $default = '')
    {
        if (!isset($this->getValues()[$nth])) {
            return $default;
        }

        return $this->getValues()[$nth];
    }

    /**
     * @return int
     */
    public function getValuesCount()
    {
        return count($this->getValues());
    }
}
