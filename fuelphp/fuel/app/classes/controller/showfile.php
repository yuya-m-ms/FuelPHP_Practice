<?php

/**
* File Viewer
*/
class Controller_ShowFile extends Controller
{
    public function action_index($filename='index.php')
    {
        $file = DOCROOT . $filename;
        $content = file_get_contents($file);
        $view = View::forge('showfile');
        $view->set('title', "File Viewer");
        $view->set('content', $content);
        return $view;
    }
}