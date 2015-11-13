<?php

namespace RightSignature\Util;

/**
 * Various array-manipulation functions.
 */
class ArrayHelpers
{
    /**
     * Returns true if $array has any string keys.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssociative($array)
    {
        return is_array($array) && !ctype_digit(implode('', array_keys($array)));
    }

    /**
     * Like array_map, but $callable accepts two args (key and value,
     * respectively) and must return a (key, value) tuple.
     *
     * @param array    $array
     * @param callable $callable
     */
    public static function mapKeyValues($array, $callable)
    {
        if (!is_array($array)) {
            return $array;
        }

        $mapped = array();
        foreach ($array as $key => $value) {
            list($mappedKey, $mappedValue) = call_user_func($callable, $key, $value);
            $mapped[$mappedKey] = $mappedValue;
        }

        return $mapped;
    }

    /**
     * Recursively convert all hyphen-delimited keys to underscore_delimited keys.
     *
     * @param array $array
     *
     * @return array
     */
    public static function normaliseKeys($array)
    {
        $self = __CLASS__;

        $normalise = function ($k, $v) use ($self) {
            $newKey = is_string($k) ? str_replace('-', '_', $k) : $k;
            $newVal = is_array($v) ? $self::normaliseKeys($v) : $v;

            return array($newKey, $newVal);
        };

        return self::mapKeyValues($array, $normalise);
    }

    /**
     * Eliminates a redundant intermediate array in a nested array structure. E.g.
     * converts array('merge-fields' => array('merge-field' => array(1, 2, 3)))
     * to array('merge-fields' => array(1, 2, 3)).
     *
     * @param array  $array
     * @param string $key
     *
     * @return array
     */
    public static function collapseGroup($array, $key)
    {
        if (!isset($array[$key])) {
            return $array;
        }

        $groupContents = array_values($array[$key]);
        // Ensure only a single intermediate array exists
        assert(count($groupContents == 1));
        $groupMembers = $groupContents[0];

        // Wrap singleton group member in an array
        $array[$key] = self::isAssociative($groupMembers)
            ? array($groupMembers)
            : $groupMembers;

        return $array;
    }

    /**
     * Throw a RightSignature\Exception if $key is not set in $array.
     *
     * @param array $array
     * @param mixed $key
     */
    public static function ensureIsSet($array, $key)
    {
        if (!isset($array[$key])) {
            throw new Exception("Missing required key: '$key'");
        }
    }
}
