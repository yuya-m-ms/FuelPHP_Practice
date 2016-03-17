<?php

/**
* TODO app controller for single-page app
*/
class Controller_Todo extends Controller
{
    /**
     * Fetch all alive ToDos from DB
     * @return ORM object
     */
    private function fetch_alive()
    {
        return Model_Todo::query()->where('deleted', '=', false);
    }

    /**
     * Fetch TODOs from DB
     * @return iterator of TODOs
     */
    public function fetch_todo()
    {
        return $this->fetch_alive()->get();
    }

    public function action_index()
    {
        $data['todos'] = $this->fetch_todo();
        $view = View::forge('todo', $data);
        return $view;
    }

    public function redirect_when_no_post()
    {
        if (Input::method() != 'POST') {
            return Response::redirect('todo', '405');
        }
    }

    public function action_add()
    {
        $this->redirect_when_no_post();

        $val = $this->forge_validation();
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

        return Response::redirect('todo');
    }

    /**
     * update todo by id
     * @param  int $id      of Todo
     * @param  [attrivute => value, ...] $updates attributes to be updated
     */
    private function alter($id, $updates)
    {
        // suppose no missing id
        $todo = Model_Todo::find($id);
        foreach ($updates as $attr => $value) {
            $todo->$attr = $value;
        }
        $todo->save();
    }

    public function action_delete($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, ['deleted' => true]);
        return Response::redirect('todo');
    }

    public function action_done($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, ['status_id' => 1]);
        return Response::redirect('todo');
    }

    public function action_undone($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, ['status_id' => 0]);
        return Response::redirect('todo');
    }

    public function action_change($id)
    {
        $this->redirect_when_no_post();

        $val = $this->forge_validation();
        if ($val->run()) {
            $input = $val->validated();
            $due_daytime   = $input['due_day'] . ' ' . $input['due_time'];
            $new_status_id = $input['new_status_id'];
            $this->alter($id, [
                'name'      => $input['name'],
                'due'       => Util_String::null_if_blank($due_daytime),
                'status_id' => $new_status_id,
            ]);
        }

        return Response::redirect('todo');
    }

    public function action_to_change($id)
    {
        $todo = Model_Todo::find($id);
        $data['task_to_be_changed']['id']   = $todo->id;
        $data['task_to_be_changed']['name'] = $todo->name;
        $data['task_to_be_changed']['due']  = $todo->due;
        list($due_day, $due_time) = Util_String::chop_datetime($todo->due);
        $data['task_to_be_changed']['due_day']  = $due_day;
        $data['task_to_be_changed']['due_time'] = $due_time;
        $data['task_to_be_changed']['new_status_id'] = $todo->status_id;
        $data['todos'] = $this->fetch_todo();
        return View::forge('todo', $data);
    }

    /**
     * @return Vadidation for a new task
     */
    public function forge_validation()
    {
        $val = Validation::forge();

        $val->add('name', "Task name")
            ->add_rule('trim')
            ->add_rule('required')
            ->add_rule('max_length', 100);
        $val->add('due_day', "Due day");
        $val->add('due_time', "Due time");
        $val->add('new_status_id', "New status ID");

        return $val;
    }

    private function fetch_filtered($status_id)
    {
        return $this->fetch_alive()->where('status_id', '=', $status_id)->get();
    }

    public function action_filter()
    {
        $this->redirect_when_no_post();

        $status = Input::post('status');
        if ($status == 'all') {
            return Response::redirect('todo');
        }
        $status_id         = Model_Todo::$status[$status];
        $data['status_id'] = $status_id;
        $data['todos']     = $this->fetch_filtered($status_id);

        return View::forge('todo', $data);
    }

    public function action_sort()
    {
        $this->redirect_when_no_post();

        $attr = Input::post('attr');
        $dir  = Input::post('dir');
        $data['todos'] = $this->fetch_alive()->order_by($attr, $dir)->get();

        return View::forge('todo', $data);
    }

    public function action_reset()
    {
        Response::redirect('todo');
    }
}
