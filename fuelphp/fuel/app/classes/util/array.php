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
}