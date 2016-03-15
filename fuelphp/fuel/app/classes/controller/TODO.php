<?php

function is_blank($str)
{
    return empty(trim($str));
}

function null_if_blank($str)
{
    return !is_blank($str) ? $str : null;
}

/**
* TODO app controller for single-page app
*/
class Controller_Todo extends Controller
{

    public function action_index()
    {
        $todos = Model_Todo::find('all');
        $data['todos'] = $todos;
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
        if ($val->run()) {
            $input = $val->validated();
            $input['due_daytime'] = $input['due_day'] . ' ' . $input['due_time'];

            $todo = Model_Todo::forge();
            $todo->name = $input['name'];
            $todo->due = null_if_blank($input['due_daytime']);
            $todo->status_id = 0; // = open
            $todo->deleted = false;
            $todo->save();
        } else {
            $data['html_error'] = $val->error();
            return View::forge('todo', $data);
        }

        return Response::redirect('todo');
    }

    private function alter($id, $attr, $value)
    {
        // suppose no missing id
        $todo = Model_Todo::find($id);
        $todo->$attr = $value;
        $todo->save();
    }

    public function action_delete($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, 'deleted', true);
        return Response::redirect('todo');
    }

    public function action_done($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, 'status_id', 1);
        return Response::redirect('todo');
    }

    public function action_undone($id)
    {
        $this->redirect_when_no_post();
        $this->alter($id, 'status_id', 0);
        return Response::redirect('todo');
    }

    public function action_change($id)
    {
        $this->redirect_when_no_post();

        $val = $this->forge_validation();
        if ($val->run()) {
            $input = $val->validated();
            $input['due_daytime'] = $input['due_day'] . ' ' . $input['due_time'];
            // suppose no missing id
            $todo = Model_Todo::find($id);
            $todo->name = $input['name'];
            $todo->due = null_if_blank($input['due_daytime']);
            $todo->save();
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
        $data['todos'] = Model_Todo::find('all');
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

        return $val;
    }
}
