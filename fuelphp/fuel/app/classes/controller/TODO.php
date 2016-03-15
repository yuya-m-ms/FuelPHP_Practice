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

        if (Input::method() === 'POST') {
            $val = $this->forge_validation();
            if ($val->run()) {
                $input = $val->validated();

                $todo = Model_TODO::forge();
                $todo->name = $input['name'];
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

    public function action_delete($id)
    {
        if (Input::method() === 'POST') {
            // suppose no missing id
            $todo = Model_TODO::find($id);
            $todo->deleted = true;
            $todo->save();
        }

        return Response::redirect('TODO');
    }

    public function action_done($id)
    {
        if (Input::method() === 'POST') {
            // suppose no missing id
            $todo = Model_TODO::find($id);
            $todo->status_id = 1; // done
            $todo->save();
        }

        return Response::redirect('TODO');
    }

    public function action_undone($id)
    {
        if (Input::method() === 'POST') {
            // suppose no missing id
            $todo = Model_TODO::find($id);
            $todo->status_id = 0; // open
            $todo->save();
        }

        return Response::redirect('TODO');
    }

    public function action_change($id)
    {
        if (Input::method() === 'POST') {
            $val = $this->forge_validation();
            if ($val->run()) {
                $input = $val->validated();
                // suppose no missing id
                $todo = Model_TODO::find($id);
                $todo->name = $input['name'];
                $todo->save();
            }
        }

        return Response::redirect('TODO');
    }

    // TODO: use POST?
    public function action_to_change($id)
    {
        $todo = Model_TODO::find($id);
        $data['task_to_be_changed']['id']   = $todo->id;
        $data['task_to_be_changed']['name'] = $todo->name;

        $data['TODOs'] = Model_TODO::find('all');
        return Response::forge(View::forge('TODO', $data));
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

        return $val;
    }
}
