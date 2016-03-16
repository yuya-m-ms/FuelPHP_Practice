<?php

/**
* Model dealing with DB
*/
class Model_Todo extends Orm\Model
{
    protected static $_table_name = 'todo';
    protected static $_properties = ['id', 'name', 'due', 'status_id', 'deleted'];

    // BiMap?
    static $status;
}

// unable to initialize status by evaluation in-place. OMG
$__init_status = function () {
    $s = [
        0 => 'open',
        1 => 'done',
        2 => 'pending',
        3 => 'working',
        4 => 'confirming',
    ];
    return array_merge($s, array_flip($s));
};
Model_Todo::$status = $__init_status();
