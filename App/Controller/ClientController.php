<?php


namespace App\Controller;


use Core\Session\Session;

class ClientController
{

    public function test()
    {
        $session = new Session();
        print_r($session->get('davo'));

//        print_r('davoo');
    }

    public function fef()
    {

    }
}