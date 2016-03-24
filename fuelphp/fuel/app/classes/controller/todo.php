<?php

/**
* TODO app controller for single-page app
*/
class Controller_Todo extends Controller
{
    function before()
    {
        $this->logic = Model_Todo_Logic::forge();
    }

    public function action_index()
    {
        $data['todos'] = $this->logic->fetch_todo();
        return View::forge('todo', $data);
    }

    protected function redirect_when_no_post()
    {
        if (Input::method() != 'POST') {
            Response::redirect('todo', '405');
        }
    }

    public function action_add()
    {
        $this->redirect_when_no_post();

        $val = $this->logic->$validator;
        if (!$val->run()) {
            $data['html_error'] = $val->error();
            return View::forge('todo', $data);
        } else {
            $input = $val->validated();
            $input['user_id'] = Session::get('user_id');
            $this->logic->add_todo($input);
        }

        Response::redirect('todo');
    }

    public function action_delete($id)
    {
        $this->redirect_when_no_post();
        $this->logic->alter($id, ['deleted' => true]);
        Response::redirect('todo');
    }

    public function action_done($id)
    {
        $this->redirect_when_no_post();
        $this->logic->alter($id, ['status_id' => 1]);
        Response::redirect('todo');
    }

    public function action_undone($id)
    {
        $this->redirect_when_no_post();
        $this->logic->alter($id, ['status_id' => 0]);
        Response::redirect('todo');
    }

    public function action_change($id)
    {
        $this->redirect_when_no_post();

        $val = $this->logic->$validator;
        if (!$val->run()) {
            $data['html_error'] = $val->error();
            return View::forge('todo', $data);
        } else {
            $input = $val->validated();
            $this->logic->change_todo($id, $input);
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
        $data['todos'] = $this->logic->fetch_todo();
        return View::forge('todo', $data);
    }

    public function action_to_search()
    {
        $this->redirect_when_no_post();

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
        $data['todos'] = $this->logic->search($filter_status, $sort_key, $sort_dir);
        return View::forge('todo', $data);
    }

    public function action_csv()
    {
        $run_download_as = $this->logic->forge_export_all_user_todo_as_csv();
        $run_download_as('all_todo.csv');
    }
}
