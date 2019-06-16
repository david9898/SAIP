<?php


namespace Core\Notification;


use Core\Session\Session;

class Notification
{

    public static function display()
    {
        $session = new Session();

        $flashMessage = $session->getFlashMessage();

        if ( $flashMessage ) {
            $type    = $flashMessage['key'];
            $message = $flashMessage['message'];

            echo '<script>$(document).ready(() => { toastr.' . $type . '("' . $message . '")' . '})</script>';

            return;
        }

        return false;
    }
}