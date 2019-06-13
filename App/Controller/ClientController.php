<?php


namespace App\Controller;


use App\Repository\AbonamentRepository;
use App\Repository\TownRepository;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;
use Core\Session\Session;

class ClientController extends AbstractController
{

    public function test()
    {
        $session = new Session();
        print_r($session->get('davo'));

//        print_r('davoo');
    }

    public function showClients()
    {
        $this->render('Clients/clientsPaginationTemplate.php', [
            'css' => [
                'Public/css/clients.css',
                'Public/css/header.css'
            ]
        ]);
    }

    public function addClient(PrepareStatementInterface $db)
    {
        $townRepo      = new TownRepository($db);
        $abonamentRepo = new AbonamentRepository($db);

        $towns      = $townRepo->getTowns();
        $abonaments = $abonamentRepo->getAbonaments();
        $csrf_token = $this->generateCsrfToken();

        $this->render('Clients/addClient.php', [
            'css'        => [
                'Public/css/header.css',
                'Public/css/addClient.css',
                'node_modules/easy_autocomplete/dist/easy-autocomplete.css'
            ],
            'js'         => [
                'node_modules/jquery/dist/jquery.js',
                'node_modules/easy_autocomplete/dist/jquery.easy-autocomplete.js'
            ],
            'towns'      => $towns,
            'abonaments' => $abonaments,
            'csrf_token' => $csrf_token
        ]);
    }
}