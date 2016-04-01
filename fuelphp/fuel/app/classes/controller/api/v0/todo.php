<?php

/**
* REST API
*/
class Controller_Api_V0_Todo extends Controller_Rest
{
    public function before()
    {
        date_default_timezone_set('UTC');
        parent::before();
        Domain_Todo::before();
    }

    public function get_item($id)
    {
        return $this->response(Model_Todo::find($id));
    }

    public function get_list()
    {
        $user_id = Session::get('user_id') ?: 0;
        $todos   = Domain_Todo::fetch_todo($user_id);
        return $this->response($todos);
    }

    public function get_list_me()
    {
        // on session
        return $this->get_list();
    }

    public function get_list_user($id)
    {
        $todos   = Domain_Todo::fetch_todo($id);
        return $this->response($todos);
    }

    public function delete_item($id)
    {
        Domain_Todo::alter($id, ['deleted' => true]);
    }

    public function post_item()
    {
        $item = [
            'name'      => Input::get('name'),
            'due'       => Input::get('due'),
            'status_id' => 0,
            'deleted'   => false,
            'user_id'   => Session::get('user_id'),
        ];
        Domain_Todo::add_todo($item);
    }

    public function put_item($id)
    {
        $item = [
            'name'      => Input::get('name'),
            'due'       => Input::get('due'),
            'status_id' => Input::get('status_id'),
            'deleted'   => Input::get('deleted'),
            'user_id'   => Input::get('user_id'),
        ];
        Domain_Todo::alter($id, $item);
    }

    public function patch_item($id)
    {
        $item = [
            'name'      => Input::get('name'),
            'due'       => Input::get('due'),
            'status_id' => Input::get('status_id'),
            'deleted'   => Input::get('deleted'),
            'user_id'   => Input::get('user_id'),
        ];
        $non_null = function ($value) {
            return ! is_null($value);
        };
        array_filter($item, $non_null);
        Domain_Todo::alter($id, $item);
    }
}