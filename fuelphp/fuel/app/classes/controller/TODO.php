<?php

/**
* TODO app controller for single-page app
*/
class Controller_TODO extends Controller
{

    function action_index()
    {
        $view = View::forge('TODO');
        return $view;
    }
}