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
        $data['task_to_be_changed']['id']   = $todo->id;
        $data['task_to_be_changed']['name'] = $todo->name;
        $data['task_to_be_changed']['due']  = $todo->due;
        list($due_day, $due_time) = Model_Todo_Logic::chop_datetime($todo->due);
        $data['task_to_be_changed']['due_day']   = $due_day;
        $data['task_to_be_changed']['due_time']  = $due_time;
        $data['task_to_be_changed']['status_id'] = $todo->status_id;
        $data['todos'] = Model_Todo_Logic::fetch_todo();
        return View::forge('todo', $data);
    }

    public function action_filter()
    {
        self::redirect_when_no_post();

        $status = Input::post('status');
        if ($status == 'all') {
            Response::redirect('todo');
        }
        $status_id      = Model_Todo_Logic::$status_bimap[$status];
        $data['status'] = $status;
        $data['todos']  = Model_Todo_Logic::fetch_filtered_by($status_id);

        return View::forge('todo', $data);
    }

    public function action_sort()
    {
        self::redirect_when_no_post();

        $attr = Input::post('attr');
        $dir  = Input::post('dir');
        $data['todos'] = Model_Todo_Logic::fetch_ordered_by($attr, $dir);

        return View::forge('todo', $data);
    }
}
