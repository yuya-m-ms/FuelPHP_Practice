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
        $this->load_user_info(Session::get('user_info.user_id'));
        $view->set('user_info_json', Session::get('user_info.json'));
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
        // check CSRF token; true if invalid
        if (Security::check_token(Session::get('access.state'))) {
            Response::redirect('practice4/logout');
        }
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

    /**
     * Load user info on Sesson from AWS S3
     * @param  string $user_id of Google
     */
    protected function load_user_info($user_id)
    {
        if (empty(Session::get('user_info.user_id'))) {
            return;
        }
        $user_id = Session::get('user_info.user_id');
        $key     = 'practice-4/user_'.$user_id.'.json';
        $stored  = Domain_Practice4::fetch_from_AWS_S3($key);
        if (empty($stored)) {
            $json = self::pretty_json([
                'user_id' => $user_id,
                'email'   => Session::get('user_info.email'),
            ]);
            Domain_Practice4::store_to_AWS_S3($key, $json);
            $stored = $json;
        }
        Session::set('user_info.json', $stored);
        $user_info = json_decode($stored, boolval('as_assoc'));
        Session::set('user_info.user_id', $user_info['user_id']);
        Session::set('user_info.email', $user_info['email']);
    }
}