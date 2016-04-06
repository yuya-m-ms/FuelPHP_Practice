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
        $view->set('google_oauth_url', Domain_Practice4::forge_login_url());

        // login check
        $state = Session::get('google_oauth.state');
        Profiler::console('Input: '.json_encode(Input::get()));
        $is_logged_in = boolval($state);
        $view->set('is_logged_in', $is_logged_in);
        Profiler::console('Logged_in? = '.($is_logged_in ? 'true' : 'false'));
        $view->set('login_status');
        $view->set('state', $state);
        $view->set('input_get', Input::get());
        if ($code = Input::get('code')) {
            $view->set('data', $this->fetch_from_google($code));
        } else {
            $view->set('data', null);
        }
        // $view->set('data', null);
        $view->set('username');

        return $view;
    }

    protected function fetch_from_google($code)
    {
        $url  = Domain_Practice4::forge_token_url($code);
        $curl = Request::forge($url, 'curl');
        $curl->set_method('post');
        $res = $curl->execute()->response();
        return ($res->status == 200) ? $res->body : '';
    }
}