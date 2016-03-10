<?php

/**
* Practice 1
*/
class Controller_Practice1 extends Controller
{

    public function action_index()
    {
        $view = View::forge('practice1');
        $view->set('title', "演習1");
        $view->set('content', "演習1");
        return $view;
    }

    public function action_hello()
    {
        $view = View::forge('practice1');
        $view->set('title', "演習1");
        $view->set('content', "Hello, world!");
        return $view;
    }

    public function action_datetime()
    {
        $view = View::forge('practice1');
        $view->set('title', "演習1");
        $date = new Datetime();
        $view->set('content', $date->format("Y/m/d H:i:s"));
        return $view;
    }

    public function action_404()
    {
        $view = View::forge('practice1');
        $view->set('title', "演習1");
        $view->set('content', "お探しのページは見つかりませんでした");
        return $view;
    }

}