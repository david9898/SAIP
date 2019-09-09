<?php


namespace Core\Controller;


use Core\Exception\AccessDenyException;
use Core\Notification\Notification;
use Core\Session\Session;

abstract class AbstractController
{

    protected function render($template, $data = null)
    {
        require_once 'App/Template/Basic/headerTemplate.php';
        require_once 'App/Template/' . $template;
        Notification::display();
        require_once 'App/Template/Basic/footerTemplate.php';

        return;
    }

    protected function redirect($url)
    {
        $baseUrl = 'http://linda.itn.bg';

        header("Location: " . $baseUrl . $url);

        exit();
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

    protected function validateAccess(bool $isLog, $role = null)
    {
        $session = new Session();

        if ( $isLog ) {
            if ( $role !== null ) {
                $roles = $session->get('userData')['roles'];

                if ( in_array('ROLE_ADMIN', $roles) ) {
                    return true;
                }else if ( in_array($role, $roles) ) {
                    return true;
                }else {
                    throw new AccessDenyException('You have no access');
                }
            }else {
                $userData = $session->get('userData');

                if ( $userData !== null ) {
                    return true;
                }else {
                    $this->redirect('/login');
                }
            }
        }else {
            if ( $session->checkIfKeyExist('userData') ) {
                $this->redirect('/clients');
            }else {
                return true;
            }
        }
    }

    protected function baseRender(string $template, $data = null)
    {
        require_once 'App/Template/' . $template;
        Notification::display();
    }
}