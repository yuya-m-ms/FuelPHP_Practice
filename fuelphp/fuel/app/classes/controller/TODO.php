<?php

/**
* TODO app controller for single-page app
*/
class Controller_TODO extends Controller
{

    public function action_index()
    {
        $TODOs = Model_TODO::find('all');
        $data['TODOs'] = $TODOs;
        $view = View::forge('TODO', $data);
        return $view;
    }

    public function action_add()
    {
        if (Input::method() === 'GET') {
            return Response::forge(View::forge('todo/add'));
        }

        $data = [];

        if (Input::method() === 'POST') {
            $val = $this->forge_validation();
            if ($val->run()) {
                $input = $val->validated();
                $input['due_daytime'] = $input['due_day'] . ' ' . $input['due_time'];
                // echo '<pre>', var_dump($input), '</pre>';
                $data['input'] = $input;
                // return View::forge('todo/add', $data);

                $todo = Model_TODO::forge();
                $todo->name = $input['name'];
                $todo->due = $input['due_daytime'];
                $todo->status_id = 0; // = open
                $todo->deleted = false;
                $todo->save();
            } else {
                $data['error'] = $val->error();
                return View::forge('todo/add', $data);
            }
        }

        return Response::redirect('TODO');
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
