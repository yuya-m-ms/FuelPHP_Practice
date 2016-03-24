<?php

/**
* Model dealing with DB
*/
class Model_Todo extends Orm\Model
{
    protected static $_table_name = 'todo';
    protected static $_properties = ['id', 'name', 'due', 'status_id', 'deleted', 'user_id'];
    protected static $_has_one = [
        'status' => [
            'model_to' => 'Model_Todo_Status',
            'key_from' => 'status_id',
            'key_to'   => 'id',
        ]
    ];
}
