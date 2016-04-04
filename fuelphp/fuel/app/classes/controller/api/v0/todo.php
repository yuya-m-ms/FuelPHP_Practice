<?php

/**
* REST API.
* Create系の操作はTodo ItemのURIを返す
*/
class Controller_Api_V0_Todo extends Controller_Rest
{
    protected static $host;
    protected static $uri_item;

    public function before()
    {
        date_default_timezone_set('UTC');
        parent::before();
        Domain_Todo::before();
        static::$host = static::$host ?: 'http://'.Input::server('HTTP_HOST');
        static::$uri_item = static::$uri_item ?: static::$host.'api/v0/todo/item/';
    }

    protected function response_with_uri($body = null, $status = 200, $item_id)
    {
        $res = $this->response($body, $status);
        $res->set_header('Location', static::$uri_item.$item_id);
        return $res;
    }

    public function get_item($id)
    {
        $item = Domain_Todo::fetch_item($id);
        if ($item->deleted) {
            return $this->response(null, 410); // Gone = deleted
        }
        $body = ['item' => $item];

        return $this->response($body);
    }

    public function get_list($user = 'user', $id = 0)
    {
        switch ($user) {
            case 'user':
                $user_id = $id;
                break;
            case 'me':
                $user_id = Session::get('user_id');
                break;
            default:
                $user_id = Session::get('user_id') ?: 0;
                break;
        }
        $todos = Domain_Todo::fetch_todo($user_id);
        $body  = ['list' => $todos];

        return $this->response($body);
    }

    public function delete_item($id)
    {
        Domain_Todo::alter($id, ['deleted' => true]);
        return $this->response(null, 204);
    }

    public function post_item()
    {
        $item = [
            'name'      => Input::post('name'),
            'due'       => Input::post('due'),
            'status_id' => 0,
            'deleted'   => false,
            'user_id'   => Session::post('user_id'),
        ];
        $id   = Domain_Todo::add_todo($item);
        $body = ['item' => Domain_Todo::fetch_item($id)];

        return $this->response_with_uri($body, 201, $id);
    }

    public function put_item($id)
    {
        $item = [
            'name'      => Input::put('name'),
            'due'       => Input::put('due'),
            'status_id' => Input::put('status_id'),
            'deleted'   => Input::put('deleted'),
            'user_id'   => Input::put('user_id'),
        ];
        $id   = Domain_Todo::alter($id, $item);
        $body = ['item' => Domain_Todo::fetch_item($id)];

        return $this->response_with_uri($body, 200, $id);
    }

    public function patch_item($id)
    {
        $no_change = 'NO_CHANGE';
        // no key = no change
        $item = [
            'name'      => Input::patch('name',      $no_change),
            'due'       => Input::patch('due',       $no_change),
            'status_id' => Input::patch('status_id', $no_change),
            'deleted'   => Input::patch('deleted',   $no_change),
            'user_id'   => Input::patch('user_id',   $no_change),
        ];
        $keep = function ($value) use ($no_change) {
            return ($value !== $no_change);
        };
        array_filter($item, $keep);
        $id   = Domain_Todo::alter($id, $item);
        $body = ['item' => Domain_Todo::fetch_item($id)];

        return $this->response_with_uri($body, 200, $id);
    }
}