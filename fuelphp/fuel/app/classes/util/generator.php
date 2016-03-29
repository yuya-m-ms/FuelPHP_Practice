<?php

/**
* generator utility
*/
class Util_Generator
{
    /**
     * Cartesian product
     * e.g. ([A, B], [1, 2, 3]) => [A, 1]; [A, 2]; [A, 3]; [B, 1]; [B, 2]; [B, 3]
     * @param  ...[mixed] sets for cross product
     * @return generator of Cartesian product: ...[mixed]
     */
    public static final function cartesian_product()
    {
        $vectors = func_get_args();
        if (empty($vectors)) {
            yield [];
        } else {
            list($head, $tail) = Util_Array::head_tail($vectors);
            foreach ($head as $h) {
                $subs = forward_static_call_array('static::cartesian_product', $tail);
                foreach ($subs as $vec) {
                    yield array_merge([$h], $vec);
                }
            }
        }
    }
}
