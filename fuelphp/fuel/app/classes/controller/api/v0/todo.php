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
        // triggered in LIFO on shutdown
        Event::register('shutdown', $fatal_error_500 = function () {
            $error = error_get_last()['type'];
            $fatal = $error === E_ERROR or $error === E_USER_ERROR;
            if ( ! $fatal) { return; }
            $ob  = ob_get_clean(); // stash Fatal Error Message
            $res = $this->response(['errors' => [
                'FATAL_ERROR',
                'traces' => array_filter(explode("\n", $ob)),
            ]], 500);
            $res->send(true);
            exit();
        });

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

    protected static function body_item($id)
    {
        return ['item' => Domain_Todo::fetch_item($id)];
    }

    protected static function body_list($user_id)
    {
        return ['list' => Domain_Todo::fetch_todo($id)];
    }

    public function get_item($id)
    {
        try {
            $item = Domain_Todo::fetch_item($id);
        } catch (InvalidIdException $e) {
            $error_list = ['errors' => ['Invalid Item ID']];
            return $this->response($error_list, 404);
        }
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
            case '':
                $user_id = Session::get('user_id') ?: 0;
                break;
            default:
                $error_list = ['errors' => ['Invalid User']];
                return $this->response($error_list, 405);
        }
        $body = static::body_list($user_id);

        return $this->response($body);
    }

    public function delete_item($id)
    {
        try {
            Domain_Todo::alter($id, ['deleted' => true]);
            return $this->response(null, 204);
        } catch (InvalidIdException $e) {
            $error_list = ['errors' => ['Invalid Item ID']];
            return $this->response($error_list, 404);
        }
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
        $body = static::body_item($id);

        return $this->response_with_uri($body, 201, $id);
    }

    public function put_item($id)
    {
        $no_change = ' NO_CHANGE ';
        // no key = no change; input has no trailing space
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
        $body = static::body_item($id);

        return $this->response_with_uri($body, 200, $id);
    }
}