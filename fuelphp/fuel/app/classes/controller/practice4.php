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
            if (empty($code = Input::get('code'))) { return $view; }
            $token_req = Domain_Practice4::fetch_token($code);
            $view->set('data', $token_req);
            if (empty($token_req) or  ! $token_req['expires_in'] > 0) { return $view; }
            $user_info = $this->fetch_from_google($token_req['id_token']);
            $view->set('user_info', $user_info);
            $view->set('username', $user_info['user_id']);
            $view->set('email', $user_info['email']);
        }

        return $view;
    }

    protected function fetch_from_google($token)
    {
        return Domain_Practice4::fetch_user_info($token);
    }
}