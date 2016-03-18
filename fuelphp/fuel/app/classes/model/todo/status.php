<?php

/**
* Model dealing with DB
*/
class Model_Todo_Status extends Orm\Model
{
    protected static $_table_name = 'todo_status';
    protected static $_properties = ['id', 'name'];

    static $status;
}

// unable to initialize status by evaluation in-place
Model_Todo_Status::$status = array_map(
    function ($row) {
        return $row->name;
    }, Model_Todo_Status::query()->select('name')->get()
);
