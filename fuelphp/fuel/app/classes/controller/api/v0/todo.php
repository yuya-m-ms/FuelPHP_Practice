<?php

/**
* REST API
*/
class Controller_Api_V0_Todo extends Controller_Rest
{
    public function before()
    {
        parent::before();
        Domain_Todo::before();
    }

    public function get_todo($id)
    {
        return;
    }

    public function get_todo_list()
    {
        return;
    }

    public function delete_todo($id)
    {
        return;
    }

    public function post_todo()
    {
        return;
    }

    public function put_todo($id)
    {
        return;
    }

    public function patch_todo($id)
    {
        return;
    }
}