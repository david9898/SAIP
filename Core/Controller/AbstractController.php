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
        $baseUrl = 'http://localhost:82/Network_Project';

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
            if ( $session->checkIfKeyExist('userData') ) {
                if ( $role !== null ) {
                    $userData = $session->get('userData');

                    if ( $role === 'ROLE_CUSTOMER' ) {
                        if ( $userData['role'] === 'ROLE_CUSTOMER' || $userData['role'] === 'ROLE_ADMIN' ) {
                            return true;
                        }
                    }else if ( $role === 'ROLE_ADMIN' ) {
                        if ( $userData['role'] === 'ROLE_ADMIN' ) {
                            return true;
                        }else {
                            throw new AccessDenyException('You have no access!!!');
                        }
                    }
                }else {
                    return true;
                }
            }else {
                $this->redirect('/login');
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