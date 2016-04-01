<?php

/**
* TODO app controller for single-page app
*/
class Controller_Todo extends Controller
{
    public function before()
    {
        Domain_Todo::before();
    }

    public function action_index()
    {
        return $this->forge_todo_view();
    }

    protected function forge_todo_view($data = [])
    {
        $user_id = Session::get('user_id') ?: 0;
        if ( ! array_key_exists('todos', $data)) {
            $data['todos'] = Domain_Todo::fetch_todo($user_id);
        }
        $data['status_list'] = Domain_Todo::get('status_list');
        $data['user_id'] = $user_id;
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

        $val = Domain_Todo::get('validator');
        if ( ! $val->run()) {
            $data['html_error'] = $val->error();
            return $this->forge_todo_view($data);
        } else {
            $input = $val->validated();
            $input['user_id'] = Session::get('user_id');
            Domain_Todo::add_todo($input);
        }

        Response::redirect('todo');
    }

    public function action_delete($id)
    {
        $this->redirect_when_no_post();
        Domain_Todo::alter($id, ['deleted' => true]);
        Response::redirect('todo');
    }

    public function action_done($id)
    {
        $this->redirect_when_no_post();
        Domain_Todo::alter($id, ['status_id' => 1]);
        Response::redirect('todo');
    }

    public function action_undone($id)
    {
        $this->redirect_when_no_post();
        Domain_Todo::alter($id, ['status_id' => 0]);
        Response::redirect('todo');
    }

    public function action_change($id)
    {
        $this->redirect_when_no_post();

        $val = Domain_Todo::get('validator');
        if ( ! $val->run()) {
            $data['html_error'] = $val->error();
            return $this->forge_todo_view($data);
        } else {
            $input = $val->validated();
            Domain_Todo::change_todo($id, $input);
        }

        Response::redirect('todo');
    }

    public function action_to_change($id)
    {
        $todo = Domain_Todo::fetch_item($id);
        list($due_day, $due_time) = Domain_Todo::chop_datetime($todo->due);
        $data['task_to_be_changed'] = [
            'id'        => $todo->id,
            'name'      => $todo->name,
            'due'       => $todo->due,
            'due_day'   => $due_day,
            'due_time'  => $due_time,
            'status_id' => $todo->status_id,
        ];
        $data['statuses']    = array_map('ucwords', Domain_Todo::get('status_cache'));
        return $this->forge_todo_view($data);
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
        $user_id = Session::get('user_id');
        $data['todos'] = Domain_Todo::search($filter_status, $sort_key, $sort_dir, $user_id);
        return $this->forge_todo_view($data);
    }

    public function action_download($type)
    {
        $user_id = Session::get('user_id');
        $run_download_as = Domain_Todo::forge_download_all_todo($user_id, $type);
        $run_download_as('all_todo.'.strtolower($type));
    }
}
