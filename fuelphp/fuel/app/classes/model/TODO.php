<?php

/**
* Model dealing with DB
*/
class Model_TODO extends Orm\Model
{
    protected static $_table_name = 'TODOs';
    protected static $_properties = ['id', 'name', 'due', 'status', 'deleted'];
}