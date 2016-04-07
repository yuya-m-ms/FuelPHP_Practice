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
        // DEBUG
        $view->set('session', json_encode(Session::get(), JSON_PRETTY_PRINT));
        Profiler::console('Input: '.json_encode(Input::get(), JSON_PRETTY_PRINT));
        Profiler::console('Session: '.json_encode(Session::get(), JSON_PRETTY_PRINT));
        // login check
        $logged_in = ! empty(Session::get('user_info.user_id'));
        $view->set('logged_in', $logged_in);
        Profiler::console('Logged_in? = '.var_export($logged_in, true));
        // load user info
        $view->set('user_info', json_encode(Session::get('user_info'), JSON_PRETTY_PRINT));
        $view->set('user_id', Session::get('user_info.user_id'));
        $view->set('email', Session::get('user_info.email'));

        return $view;
    }

    public function action_login()
    {
        Profiler::console('Login');
        $state = Security::generate_token();
        $login = Domain_Practice4::forge_login_url($state);
        Response::redirect($login);
    }

    public function action_login_redirect()
    {
        // DEBUG
        Profiler::console('Login_Redirect');
        Profiler::console('Input: '.json_encode(Input::get(), JSON_PRETTY_PRINT));
        Profiler::console('Session: '.json_encode(Session::get(), JSON_PRETTY_PRINT));
        // get request token
        Session::set('access', Input::get());
        $code = Session::get('access.code');
        // get access token
        Session::set('token', Domain_Practice4::fetch_token($code));
        $token = Session::get('token.id_token');
        // get user info
        Profiler::console('Token: '.$token);
        Session::set('user_info', Domain_Practice4::fetch_user_info($token));

        Response::redirect('practice4');
    }

    public function action_logout()
    {
        Profiler::console('Logout');
        Session::destroy();
        Response::redirect('practice4');
    }

    protected function fetch_from_google($token)
    {
        return Domain_Practice4::fetch_user_info($token);
    }
}