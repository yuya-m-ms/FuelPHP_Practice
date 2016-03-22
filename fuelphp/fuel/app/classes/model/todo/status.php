<?php

/**
* Model dealing with DB
*/
class Model_Todo_Status extends Orm\Model
{
    protected static $_table_name = 'todo_status';
    protected static $_properties = ['id', 'name'];
}
