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
}