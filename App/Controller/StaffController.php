<?php


namespace App\Controller;


use App\Repository\AbonamentRepository;
use App\Repository\StaffRepository;
use App\Repository\StreetRepository;
use App\Repository\TownRepository;
use App\Service\AbonamentService;
use App\Service\StaffService;
use App\Service\StreetService;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;
use Core\Request\Request;
use Core\Session\Session;

class StaffController extends AbstractController
{

    public function login($db)
    {
        $this->validateAccess(false);

        $request = new Request();

        if ( $request->isSubmit('login') ) {
            $staffRepo    = new StaffRepository($db);
            $staffService = new StaffService();

            if ( $staffService->login($staffRepo, $request->getPOST()) ) {
                $this->redirect('/clients');
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

    public function registerStaff(PrepareStatementInterface $db)
    {
        $this->validateAccess(1, 'ROLE_ADMIN');

        $request = new Request();

        if ( $request->isSubmit('add_staff') ) {
            $staffRepository = new StaffRepository($db);
            $staffService    = new StaffService();

            $res = $staffService->registerStaff($staffRepository, $request->getPOST());

            if ( $res ) {
                $this->redirect('/clients');
            }else {
                $csrfToken = $this->generateCsrfToken();

                $this->render('Staff/registerStaff.php', [
                    'css' => [
                        'Public/css/header.css',
                        'Public/css/addStaff.css'
                    ],
                    'csrf_token' => $csrfToken
                ]);
            }
        }else {
            $csrfToken = $this->generateCsrfToken();

            $this->render('Staff/registerStaff.php', [
                'css' => [
                    'Public/css/header.css',
                    'Public/css/addStaff.css'
                ],
                'csrf_token' => $csrfToken
            ]);
        }
    }

    public function addAbonament($db)
    {
        $this->validateAccess(1, 'ROLE_ADMIN');

        $request   = new Request();

        if ( $request->isSubmit('add_abonament') ) {
            $abonamentRepo    = new AbonamentRepository($db);
            $abonamentService = new AbonamentService();

            if ( $abonamentService->addAbonament($abonamentRepo, $request->getPOST()) ) {
                $this->redirect('/clients');
            }else {
                $csrfToken = $this->generateCsrfToken();

                $this->render('Staff/addAbonamentTemplate.php', [
                    'css' => [
                        'Public/css/header.css',
                        'Public/css/addAbonament.css'
                    ],
                    'csrf_token' => $csrfToken
                ]);
            }
        }else {
            $csrfToken = $this->generateCsrfToken();

            $this->render('Staff/addAbonamentTemplate.php', [
                'css' => [
                    'Public/css/header.css',
                    'Public/css/addAbonament.css'
                ],
                'csrf_token' => $csrfToken
            ]);
        }
    }

    public function addStreet($db)
    {
        $this->validateAccess(1, 'ROLE_ADMIN');

        $request = new Request();

        if ( $request->isSubmit('add_street') ) {
            $streetRepo    = new StreetRepository($db);
            $streetService = new StreetService();

            if ( $streetService->addStreetInTown($streetRepo, $request->getPOST()) ) {
                $this->redirect('/clients');
            }else {
                $townRepo  = new TownRepository($db);
                $csrfToken = $this->generateCsrfToken();

                $this->render('Staff/addStreetTemplate.php', [
                    'css' => [
                        'Public/css/header.css',
                        'Public/css/addAbonament.css'
                    ],
                    'towns' => $townRepo->getTowns(),
                    'csrf_token' => $csrfToken
                ]);
            }
         }else {
            $townRepo  = new TownRepository($db);
            $csrfToken = $this->generateCsrfToken();

            $this->render('Staff/addStreetTemplate.php', [
               'css' => [
                   'Public/css/header.css',
                   'Public/css/addAbonament.css'
                ],
                'towns'      => $townRepo->getTowns(),
                'csrf_token' => $csrfToken
            ]);
        }
    }
}