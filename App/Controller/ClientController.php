<?php


namespace App\Controller;

use App\Repository\AbonamentRepository;
use App\Repository\BillRepository;
use App\Repository\ClientRepository;
use App\Repository\PaymentRepository;
use App\Repository\TownRepository;
use App\Service\ClientService;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;

class ClientController extends AbstractController
{
    public function showClients($db)
    {
        $this->validateAccess(true);

        $clientRepo    = new ClientRepository($db);
        $clientService = new ClientService();

        $clients       = $clientService->getClients($clientRepo);
        $csrfToken     = $this->generateCsrfToken();

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

    public function seeClient(PrepareStatementInterface $db, $id)
    {
        $this->validateAccess(1);

        $clientRepo    = new ClientRepository($db);
        $paymentRepo   = new PaymentRepository($db);
        $clientService = new ClientService();
        $client        = $clientRepo->getClient($id);
        $lastPayment   = $paymentRepo->getLastPayment($id);
        $csrfToken     = $this->generateCsrfToken();

        if ( $lastPayment !== null ) {
            $lastTime = $lastPayment->getEndTime();
        }else {
            $lastTime = null;
        }

        $this->render('Clients/clientTemplate.php', [
            'css'        => [
                'Public/css/header.css',
                'Public/css/addStaff.css'
            ],
            'js'         => [
                'node_modules/moment/min/moment-with-locales.min.js',
                'Public/js/client.js'
            ],
            'client'     => $client,
            'bills'      => $clientService->calculateBills($lastPayment, $lastTime),
            'csrf_token' => $csrfToken
        ]);
    }
}