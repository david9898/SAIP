<?php


namespace App\ApiController;


use App\Repository\ClientRepository;
use App\Repository\NeighborhoodRepository;
use App\Repository\StreetRepository;
use App\Repository\TownRepository;
use App\Service\ClientService;
use Core\Controller\AbstractController;
use Core\Request\Request;

class ClientApiController extends AbstractController
{
    public function addClient($db)
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
}