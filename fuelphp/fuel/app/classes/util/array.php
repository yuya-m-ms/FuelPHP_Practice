<?php

/**
* Array generating tools – no manipulation for safe
*/
class Util_Array
{
    /**
     * Make a reversible array: key <=> value
     * e.g. [0 => 'a'] --> [0 => 'a', 'a'=> 0]
     * @param  array $array as map
     * @return array        getting reversible
     */
    static function bimap($array)
    {
        return array_merge($array, array_flip($array));
    }

    /**
     * Make a map from an array of keys with a genegeting function.
     * @param  callback $to_value function :: $key => $value
     * @param  array $keys     as array
     * @return array           as map
     */
    static function to_map($to_value, $keys)
    {
        $map = [];
        foreach ($keys as $key) {
            $map[$key] = $to_value($key);
        }
        return $map;
    }

    // faster and more random than array_rand()
    public static function random_key($array)
    {
        $keys = array_keys($array);
        return $keys[mt_rand(0, count($keys) - 1)];
    }

    public static function random_value($array)
    {
        return $array[static::random_key($array)];
    }

    // use array_value() to re-index if needed
    public static function random_pop($array)
    {
        $key = static::random_key($array);
        $value = $array[$key];
        unset($array[$key]);
        return $value;
    }

    public static final function head_tail($array)
    {
        if ( ! is_array($array) or empty($array)) {
            return null;
        }
        $head = reset($array);
        $tail = array_slice($array, 1);
        return [$head, $tail];
    }
}
