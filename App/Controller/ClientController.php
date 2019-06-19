<?php


namespace App\Controller;


use App\Repository\AbonamentRepository;
use App\Repository\ClientRepository;
use App\Repository\TownRepository;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;

class ClientController extends AbstractController
{
    public function showClients($db)
    {
        $this->validateAccess(true);

        $clientRepo = new ClientRepository($db);

        $clients    = $clientRepo->getClients();
        $csrfToken  = $this->generateCsrfToken();

        $this->render('Clients/clientsPaginationTemplate.php', [
            'css' => [
                'Public/css/clients.css',
                'Public/css/header.css'
            ],
            'js'  => [
                'node_modules/handlebars/dist/handlebars.min.js',
                'Public/js/clients.js'
            ],
            'clients'    => $clients,
            'csrf_token' => $csrfToken
        ]);
    }

    public function addClient(PrepareStatementInterface $db)
    {
        $this->validateAccess(true);

        $townRepo      = new TownRepository($db);
        $abonamentRepo = new AbonamentRepository($db);

        $towns      = $townRepo->getTowns();
        $abonaments = $abonamentRepo->getAbonaments();
        $csrf_token = $this->generateCsrfToken();

        $this->render('Clients/addClient.php', [
            'css'        => [
                'node_modules/easy-autocomplete/dist/easy-autocomplete.min.css',
                'Public/css/header.css',
                'Public/css/addClient.css',
            ],
            'js'         => [
                'node_modules/easy-autocomplete/dist/jquery.easy-autocomplete.min.js',
                'node_modules/sweetalert/dist/sweetalert.min.js',
                'Public/js/addClient.js'
            ],
            'towns'      => $towns,
            'abonaments' => $abonaments,
            'csrf_token' => $csrf_token
        ]);
    }
}