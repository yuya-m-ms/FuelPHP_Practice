<?php

/**
* TODO app controller for single-page app
*/
class Controller_Todo extends Controller
{
    public function action_index()
    {
        $data['todos'] = Model_Todo_Logic::fetch_todo();
        return View::forge('todo', $data);
    }

    static function redirect_when_no_post()
    {
        if (Input::method() != 'POST') {
            Response::redirect('todo', '405');
        }
    }

    public function action_add()
    {
        self::redirect_when_no_post();

        $val = Model_Todo_Logic::$validator;
        if (!$val->run()) {
            $data['html_error'] = $val->error();
            return View::forge('todo', $data);
        } else {
            $input = $val->validated();
            $input['due_daytime'] = $input['due_day'] . ' ' . $input['due_time'];

            $todo = Model_Todo::forge();
            $todo->name      = $input['name'];
            $todo->due       = Util_String::null_if_blank($input['due_daytime']);
            $todo->status_id = 0; // = open
            $todo->deleted   = false;
            $todo->save();
        }

        Response::redirect('todo');
    }

    public function action_delete($id)
    {
        self::redirect_when_no_post();
        Model_Todo_Logic::alter($id, ['deleted' => true]);
        Response::redirect('todo');
    }

    public function action_done($id)
    {
        self::redirect_when_no_post();
        Model_Todo_Logic::alter($id, ['status_id' => 1]);
        Response::redirect('todo');
    }

    public function action_undone($id)
    {
        self::redirect_when_no_post();
        Model_Todo_Logic::alter($id, ['status_id' => 0]);
        Response::redirect('todo');
    }

    public function action_change($id)
    {
        self::redirect_when_no_post();

        $val = Model_Todo_Logic::$validator;
        if ($val->run()) {
            $input = $val->validated();
            $due_daytime   = $input['due_day'] . ' ' . $input['due_time'];
            $status_id = $input['status_id'];
            Model_Todo_Logic::alter($id, [
                'name'      => $input['name'],
                'due'       => Util_String::null_if_blank($due_daytime),
                'status_id' => $status_id,
            ]);
        }

        Response::redirect('todo');
    }

    public function action_to_change($id)
    {
        $todo = Model_Todo::find($id);
        list($due_day, $due_time) = Model_Todo_Logic::chop_datetime($todo->due);
        $data['task_to_be_changed'] = [
            'id'        => $todo->id,
            'name'      => $todo->name,
            'due'       => $todo->due,
            'due_day'   => $due_day,
            'due_time'  => $due_time,
            'status_id' => $todo->status_id,
        ];
        $data['todos'] = Model_Todo_Logic::fetch_todo();
        return View::forge('todo', $data);
    }

    public function action_to_search()
    {
        self::redirect_when_no_post();

        $status = Input::post('status');
        $attr   = Input::post('attr');
        $dir    = Input::post('dir');
        $url    = sprintf('todo/search/%s/%s/%s', $status, $attr, $dir);
        Response::redirect($url);
    }

    public function action_search($filter_status = 'all', $sort_key = 'name', $sort_dir = 'asc')
    {
        $data = [
            'status' => $filter_status,
            'attr'   => $sort_key,
            'dir'    => $sort_dir,
        ];
        $data['todos'] = Model_Todo_Logic::search($filter_status, $sort_key, $sort_dir);
        return View::forge('todo', $data);
    }
}
