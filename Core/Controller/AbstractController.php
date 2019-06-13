<?php


namespace Core\Controller;


use Core\Session\Session;

abstract class AbstractController
{

    protected function render($template, $data = null)
    {
        require_once 'App/Template/Basic/headerTemplate.php';
        require_once 'App/Template/' . $template;
        require_once 'App/Template/Basic/footerTemplate.php';

        return;
    }

    protected function redirect($url)
    {
        header("Location: ". $url);
    }

    protected function jsonResponce($arr)
    {
        return print_r(json_encode($arr));
    }

    protected function generateCsrfToken()
    {
        $session = new Session();

        $csrfToken = bin2hex(random_bytes(32));

        $session->set('csrf_token', $csrfToken);

        return $csrfToken;
    }
}