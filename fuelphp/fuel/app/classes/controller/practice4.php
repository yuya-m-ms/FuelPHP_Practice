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
        // login check
        $logged_in = ! empty(Session::get('user_info.user_id'));
        $view->set('logged_in', $logged_in);
        // load user info
        $view->set('user_info', self::pretty_json(Session::get('user_info')));
        $view->set('user_id', Session::get('user_info.user_id'));
        $view->set('email', Session::get('user_info.email'));

        return $view;
    }

    public function action_login()
    {
        $state = Security::generate_token();
        $login = Domain_Practice4::forge_login_url($state);
        Response::redirect($login);
    }

    public function action_login_redirect()
    {
        Session::set('access', Input::get());
        $code = Session::get('access.code');
        // get access token
        Session::set('token', Domain_Practice4::fetch_token($code));
        $token = Session::get('token.id_token');
        // get user info
        Session::set('user_info', Domain_Practice4::fetch_user_info($token));

        Response::redirect('practice4');
    }

    public function action_logout()
    {
        Session::destroy();
        Response::redirect('practice4');
    }

    private static function pretty_json($json = '')
    {
        return json_encode($json, JSON_PRETTY_PRINT);
    }
}