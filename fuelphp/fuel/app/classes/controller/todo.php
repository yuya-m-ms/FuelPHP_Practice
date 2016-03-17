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
            $todo->due       = Util_StrTool::null_if_blank($input['due_daytime']);
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
            $due_daytime = $input['due_day'] . ' ' . $input['due_time'];
            $new_status_id = $input['new_status']; // :: int -- somehow
            $this->alter($id, [
                'name'      => $input['name'],
                'due'       => Util_StrTool::null_if_blank($due_daytime),
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
        list($due_day, $due_time) = $this->chop_datetime($todo->due);
        $data['task_to_be_changed']['due_day']  = $due_day;
        $data['task_to_be_changed']['due_time'] = $due_time;
        $status = Model_Todo::$status[$todo->status_id];
        $data['task_to_be_changed']['new_status'] = $status;
        $data['todos'] = $this->fetch_todo();
        return View::forge('todo', $data);
    }

    public function chop_datetime($datetime)
    {
        $re_datetime = '/(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})/';
        preg_match($re_datetime, $datetime, $matches); // why C-like
        list(, $date, $time) = array_pad($matches, 3, null);
        return [$date, $time];
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
        $val->add('new_status', "New status");

        return $val;
    }

    private function fetch_filtered($status_id)
    {
        return $this->fetch_alive()->where('status_id', '=', $status_id)->get();
    }

    public function action_filter()
    {
        $this->redirect_when_no_post();

        $i = Input::post('status'); // from <select>
        if ($i == 0) { // 'all' is selected
            return Response::redirect('todo');
        }
        $data['post']      = Input::post();
        $data['status_id'] = $i - 1;
        $data['todos']     = $this->fetch_filtered($i - 1);

        return View::forge('todo', $data);
    }

    public function action_sort()
    {
        $this->redirect_when_no_post();

        $attr = ['name', 'due', 'status_id'][Input::post('attr')]; // from <select>
        $dir  = Input::post('dir'); // from <select>
        $data['post']  = Input::post();
        $data['todos'] = $this->fetch_alive()->order_by($attr, $dir)->get();

        return View::forge('todo', $data);
    }
}
