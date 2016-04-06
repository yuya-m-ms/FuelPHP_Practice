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
        $view->set('google_oauth', Domain_Practice4::forge_login_url());
        $is_logged_in = false;
        $view->set('is_logged_in', $is_logged_in);
        Profiler::console('Logged_in? = '.($is_logged_in ? 'true' : 'false'));
        $view->set('login_status', []);
        return $view;
    }
}