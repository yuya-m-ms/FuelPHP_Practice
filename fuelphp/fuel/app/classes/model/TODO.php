<?php

/**
* Model dealing with DB
*/
class Model_TODO extends Orm\Model
{
    protected static $_table_name = 'TODO';
    protected static $_properties = ['id', 'name', 'due', 'status_id', 'deleted'];
}