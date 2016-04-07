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
        $input_get = Input::get();
        $view->set('input_get', $input_get);
        Profiler::console('Input: '.json_encode($input_get));
        $logged_in = ! empty($input_get['code']);
        $view->set('logged_in', $logged_in);
        Profiler::console('Logged_in? = '.var_export($logged_in, true));

        if ( ! $logged_in) {
            // to log-in
            $state = Security::generate_token();
            Session::set('google_oauth.state', $state);
            $view->set('google_oauth_url', Domain_Practice4::forge_login_url($state));
        } else {
            // to load data
            if ($code = Input::get('code')) {
                $token_req = Domain_Practice4::fetch_token($code);
                $view->set('data', $token_req);
            } else {
                $view->set('data', null);
            }
            $view->set('username', $input_get['hd']);
        }

        $view->set('login_status');

        return $view;
    }

    protected function fetch_from_google($token)
    {
        return Domain_Practice4::fetch_google_data($token);
    }
}