<?php

/**
* Practice 4
*/
class Controller_Practice4 extends Controller
{

    public function before()
    {
        parent::before();
        Domain_Practice4::before();
    }

    public function action_index()
    {
        $view = View::forge('practice4');
        $view->set('title', "演習4");
        $view->set('content', Domain_Practice4::get('foo'));
        return $view;
    }
}