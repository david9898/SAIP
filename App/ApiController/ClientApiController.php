<?php


namespace App\ApiController;


use App\Repository\ClientRepository;
use App\Repository\NeighborhoodRepository;
use App\Repository\PaymentRepository;
use App\Repository\StreetRepository;
use App\Repository\TownRepository;
use App\Service\ClientService;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;
use Core\Request\Request;
use Core\Session\Session;

class ClientApiController extends AbstractController
{
    public function addClient(PrepareStatementInterface $db)
    {
        $this->validateAccess(true);

        $clientRepo       = new ClientRepository($db);
        $streetRepo       = new StreetRepository($db);
        $townRepo         = new TownRepository($db);
        $neighborhoodRepo = new NeighborhoodRepository($db);
        $clientService    = new ClientService();
        $request          = new Request();

        $responce = $clientService->addClient($clientRepo, $streetRepo, $neighborhoodRepo, $townRepo, $request->getPOST());

        return $this->jsonResponce($responce);
    }

    public function getMoreClients(PrepareStatementInterface $db, $csrfToken, $firstResult)
    {
        $this->validateAccess(true);

        $session = new Session();

        if ( $session->get('csrf_token') === $csrfToken ) {
            $clientRepo = new ClientRepository($db);

            $responce = $clientRepo->getMoreClients($firstResult);

            if ( $responce !== null ) {
                $clients = [];
                foreach ($responce as $client) {
                    $clients[] = $client;
                }
            }else {
                return $this->jsonResponce(['status' => 'success', 'lastList' => 1]);
            }

            return $this->jsonResponce(['status' => 'success', 'clients' => $clients]);

        }else {
            return $this->jsonResponce(['status' => 'error', 'description' => 'Грешен токен']);
        }
    }

    public function getSearchFriends(PrepareStatementInterface $db, $csrfToken, $firstResult, $pattern)
    {
        $this->validateAccess(true);

        $session = new Session();

        if ( $session->get('csrf_token') === $csrfToken ) {
            $clientRepo = new ClientRepository($db);

            if ( $pattern !== null ) {
                $patterns = explode(' ', urldecode($pattern));

                $clients = $clientRepo->searchFriends($patterns, $firstResult);

                if ($clients !== null) {
                    $responce = [];
                    $responce['status'] = 'success';

                    foreach ($clients as $client) {
                        if ( $client['paid'] !== null ) {
                            $diffTime = $client['paid'] - time();
                            $client['paid'] = floor($diffTime / 86400);

                            if ( $diffTime > 0 ) {
                                $client['payment'] = 'paid';
                            }

                            if ( $diffTime < -7905600 ) {
                                $client['payment'] = 'delay';
                                $client['paid']    = -91;
                            }

                            if ( $diffTime > -7905600 && $diffTime <= 0 ) {
                                $client['payment'] = 'overdue';
                            }
                        }

                        $responce['clients'][] = $client;
                    }

                    return $this->jsonResponce($responce);
                } else {

                }
            }else {
                $responce = [];
                $responce['status'] = 'success';
                $clients = $clientRepo->getMoreClients($firstResult);
                foreach ($clients as $client) {
                    if ( $client['paid'] !== null ) {
                        $diffTime = $client['paid'] - time();
                        $client['paid'] = floor($diffTime / 86400);

                        if ( $diffTime > 0 ) {
                            $client['payment'] = 'paid';
                        }

                        if ( $diffTime < -7905600 ) {
                            $client['payment'] = 'delay';
                            $client['paid']    = -91;
                        }

                        if ( $diffTime > -7905600 && $diffTime <= 0 ) {
                            $client['payment'] = 'overdue';
                        }
                    }

                    $responce['clients'][] = $client;
                }
                return $this->jsonResponce($responce);
            }
        }else {
            return $this->jsonResponce(['status' => 'error', 'description' => 'Грешен токен']);
        }
    }


    public function addPayment($db)
    {
        $this->validateAccess(1);

        $request       = new Request();
        $clientRepo    = new ClientRepository($db);
        $paymentRepo   = new PaymentRepository($db);
        $clientService = new ClientService();

        $responce = $clientService->addPayment($request, $paymentRepo, $clientRepo);

        return $this->jsonResponce($responce);
    }
}