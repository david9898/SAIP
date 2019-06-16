<?php


namespace App\Controller;


use App\Repository\StaffRepository;
use App\Service\StaffService;
use Core\Controller\AbstractController;
use Core\Request\Request;
use Core\Session\Session;

class StaffController extends AbstractController
{

    public function login($db)
    {
        $this->validateAccess(false);

        $request = new Request();

        if ( $request->isSubmit('login') ) {
            $staffRepo = new StaffRepository($db);
            $staffService = new StaffService();

            if ( $staffService->login($staffRepo, $request->getPOST()) ) {
                $this->redirect('/clients/1');
            }else {
                $this->baseRender('Staff/loginTemplate.php');
            }
        }else {
            $this->baseRender('Staff/loginTemplate.php');
        }
    }

    public function logout()
    {
        $session =  new Session();

        $session->delete('userData');

        $this->redirect('/login');
    }
}