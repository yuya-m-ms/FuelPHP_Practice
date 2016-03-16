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
}