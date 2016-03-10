<?php

/**
* return "Hello, world!"
*/
class Controller_HelloWorld extends Controller
{

    public function action_index()
    {
        // return View::forge('helloworld');
        return "Hello, world!";
    }
}