<?php


namespace Core\Controller;


abstract class AbstractController
{

    protected function render($data, $template)
    {
        require_once 'App/Template/';
    }

    protected function redirect($url)
    {
        header("Location: ". $url);
    }
}