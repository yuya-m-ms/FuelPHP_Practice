<?php

/**
* Model dealing with DB
*/
class Model_Todo extends Orm\Model
{
    protected static $_table_name = 'todo';
    protected static $_properties = ['id', 'name', 'due', 'status_id', 'deleted'];

    static $status_map;
}

// unable to initialize status by evaluation in-place. OMG
Model_Todo::$status_map = Util_Array::bimap(Model_Todo_Status::$status);
