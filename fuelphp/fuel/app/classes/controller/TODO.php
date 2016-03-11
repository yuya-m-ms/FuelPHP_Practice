<?php

/**
* TODO app controller for single-page app
*/
class Controller_TODO extends Controller
{

    function action_index()
    {
        $TODOs = Model_TODO::find('all');
        $data['TODOs'] = $TODOs;
        $view = View::forge('TODO', $data);
        return $view;
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