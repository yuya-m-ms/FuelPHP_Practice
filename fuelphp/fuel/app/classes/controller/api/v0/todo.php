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

    public function get_item($id)
    {
        return;
    }

    public function get_list()
    {
        return;
    }

    public function delete_item($id)
    {
        return;
    }

    public function post_item()
    {
        return;
    }

    public function put_item($id)
    {
        return;
    }

    public function patch_item($id)
    {
        return;
    }
}